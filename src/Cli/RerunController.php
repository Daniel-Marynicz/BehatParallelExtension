<?php

namespace DMarynicz\BehatParallelExtension\Cli;

use Behat\Behat\Tester\Cli\RerunController as BehatRerunController;
use Behat\Gherkin\Node\ScenarioLikeInterface;
use Behat\Testwork\Cli\Controller;
use DMarynicz\BehatParallelExtension\Event\AfterTaskTested;
use DMarynicz\BehatParallelExtension\Event\EventDispatcherDecorator;
use DMarynicz\BehatParallelExtension\Event\ParallelTestsCompleted;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RerunController implements Controller
{
    const SERVICE_ID = 'cli.controller.parallel_extension.re_run_controller';

    /** @var EventDispatcherDecorator */
    private $eventDispatcher;

    /** @var array<string, array<string>> */
    private $lines = [];

    /** @var Controller|BehatRerunController  */
    private $decoratedController;

    public function __construct(Controller $decoratedController, EventDispatcherDecorator $eventDispatcher)
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
        $this->eventDispatcher->addListener(ParallelTestsCompleted::COMPLETED, [$this, 'writeCache']);

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
        if ($scenarioLine !== null) {
            $line .= ':' . $scenarioLine;
        }

        $this->lines[$suiteName][] = $line;
    }

    /**
     * Writes failed tests cache.
     *
     * @return void
     */
    public function writeCache()
    {
        if (empty($this->lines)) {
            $this->writeCacheByDecoratedController();

            return;
        }

        $ref = new ReflectionClass($this->decoratedController);
        if (! $ref->hasProperty('lines')) {
            $this->writeCacheByDecoratedController();

            return;
        }

        $property = $ref->getProperty('lines');
        $property->setAccessible(true);
        $property->setValue($this->decoratedController, $this->lines);

        $this->writeCacheByDecoratedController();
    }

    private function writeCacheByDecoratedController()
    {
        if (! method_exists($this->decoratedController, 'writeCache')) {
            return;
        }

        $this->decoratedController->writeCache();
    }
}
