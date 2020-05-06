<?php

namespace DMarynicz\BehatParallelExtension\Cli;

use Behat\Behat\Tester\Cli\RerunController as BehatRerunController;
use Behat\Gherkin\Node\ScenarioLikeInterface;
use Behat\Testwork\Cli\Controller;
use DMarynicz\BehatParallelExtension\Event\AfterTaskTested;
use DMarynicz\BehatParallelExtension\Event\ParallelTestCompleted;
use DMarynicz\BehatParallelExtension\Service\EventDispatcherDecorator;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RerunController implements Controller
{
    const SERVICE_ID = 'cli.controller.parallel_extension.re_run_controller';

    /** @var EventDispatcherDecorator */
    private $eventDispatcher;

    /** @var array<string, array<string>> */
    private $lines = [];

    /** @var BehatRerunController  */
    private $decoratedController;

    public function __construct(BehatRerunController $decoratedController, EventDispatcherDecorator $eventDispatcher)
    {
        $this->decoratedController = $decoratedController;
        $this->eventDispatcher     = $eventDispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function configure(Command $command)
    {
        $this->decoratedController->configure($command);
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->eventDispatcher->addListener(
            AfterTaskTested::AFTER,
            [$this, 'collectFailedTask']
        );
        $this->eventDispatcher->addListener(ParallelTestCompleted::COMPLETED, [$this, 'writeCache']);

        return $this->decoratedController->execute($input, $output);
    }

    /**
     * Records task if it is failed.
     */
    public function collectFailedTask(AfterTaskTested $taskTested)
    {
        $process = $taskTested->getProcess();
        if ($process->isSuccessful()) {
            return;
        }

        $suiteName    = $taskTested->getTask()->getSuite()->getName();
        $featureFile  = $taskTested->getTask()->getFeature()->getFile();
        $scenarioLine = null;
        $scenario     = $taskTested->getTask()->getScenario();
        if ($scenario instanceof ScenarioLikeInterface) {
            $scenarioLine = $scenario->getLine();
        }

        $line = $featureFile;
        if ($scenarioLine) {
            $line .= ':' . $scenarioLine;
        }

        $this->lines[$suiteName][] = $line;
    }

    /**
     * Writes failed tests cache.
     */
    public function writeCache()
    {
        if (! $this->lines) {
            return $this->decoratedController->writeCache();
        }

        $ref      = new ReflectionClass($this->decoratedController);
        $property = $ref->getProperty('lines');
        $property->setAccessible(true);
        $property->setValue($this->decoratedController, $this->lines);

        return $this->decoratedController->writeCache();
    }
}
