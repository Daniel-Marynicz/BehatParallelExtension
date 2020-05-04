<?php

namespace DMarynicz\BehatParallelExtension\Cli;

use Behat\Testwork\Cli\Controller;
use Behat\Testwork\Tester\Cli\ExerciseController;
use DMarynicz\BehatParallelExtension\Event\AfterTaskTested;
use DMarynicz\BehatParallelExtension\Event\BeforeTaskTested;
use DMarynicz\BehatParallelExtension\Queue\Queue;
use DMarynicz\BehatParallelExtension\Queue\Task;
use DMarynicz\BehatParallelExtension\Service\Finder\ScenarioSpecificationsFinder;
use DMarynicz\BehatParallelExtension\Service\Task\ArgumentsBuilder;
use DMarynicz\BehatParallelExtension\Worker\WorkerPoll;
use ReflectionException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ParallelScenarioController implements Controller
{
    const SERVICE_ID = 'cli.controller.parallel_extension.parallel_scenario_exercise';

    /** @var ExerciseController */
    private $decoratedExerciseController;

    /** @var ScenarioSpecificationsFinder */
    private $specificationFinder;

    /** @var ArgumentsBuilder */
    private $argumentsBuilder;

    /** @var Queue */
    private $queue;

    /** @var WorkerPoll */
    private $poll;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var AfterTaskTested[] */
    private $failed = [];

    /** @var ProgressBar */
    private $progressBar;

    /** @var OutputInterface */
    private $output;

    /** @var InputInterface */
    private $input;

    public function __construct(
        ExerciseController $decoratedExerciseController,
        ScenarioSpecificationsFinder $specificationsFinder,
        ArgumentsBuilder $argumentsBuilder,
        WorkerPoll $poll,
        Queue $queue,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->decoratedExerciseController = $decoratedExerciseController;
        $this->specificationFinder         = $specificationsFinder;
        $this->argumentsBuilder            = $argumentsBuilder;
        $this->queue                       = $queue;
        $this->poll                        = $poll;
        $this->eventDispatcher             = $eventDispatcher;
    }

    public function configure(SymfonyCommand $command)
    {
        $this->decoratedExerciseController->configure($command);

        $command->addOption(
            'parallel-scenario',
            null,
            InputOption::VALUE_OPTIONAL,
            'How many scenario jobs run in parallel? Available values empty or integer',
            false
        )
            ->addUsage('--parallel-scenario 8')
            ->addUsage('--parallel-scenario');
    }

    /**
     * @return int|null
     *
     * @throws ReflectionException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $startInParallel = $input->getOption('parallel-scenario') !== false;
        if (! $startInParallel) {
            return $this->decoratedExerciseController->execute($input, $output);
        }

        $this->output = $output;
        $this->input  = $input;

        $this->eventDispatcher->addListener(BeforeTaskTested::BEFORE, [$this, 'beforeTaskTested']);
        $this->eventDispatcher->addListener(AfterTaskTested::AFTER, [$this, 'afterTaskTested']);

        $tasks = $this->findScenarios($input);
        foreach ($tasks as $task) {
            $task->setCommand($this->argumentsBuilder->buildArguments($input, $task->getPath()));
            $this->queue->enqueue($task);
        }

        $this->progressBar = new ProgressBar($output, count($tasks));
        ProgressBar::setFormatDefinition(
            'custom',
            " %feature%\n  %scenario%\n %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%"
        );

        $this->progressBar->setMessage('<info>Starting</info>', 'feature');
        $this->progressBar->setMessage('', 'scenario');

        $this->progressBar->setFormat('custom');

        $this->progressBar->start();

        $this->poll->start();
        $this->poll->wait();
        $output->writeln('');

        return empty($this->failed) ? 0 : 1;
    }

    public function beforeTaskTested(BeforeTaskTested $beforeTaskTested)
    {
        $task          = $beforeTaskTested->getTask();
        $featureTitle  = sprintf('<info>Feature: %s</info>', $task->getFeature()->getTitle());
        $scenarioTitle = sprintf('<info>Scenario: %s</info>', $task->getScenario()->getTitle());
        $this->progressBar->setMessage($featureTitle, 'feature');
        $this->progressBar->setMessage($scenarioTitle, 'scenario');
    }

    public function afterTaskTested(AfterTaskTested $taskTested)
    {
        $this->progressBar->advance();
        $process = $taskTested->getProcess();
        if ($process->isSuccessful()) {
            return;
        }

        $output = $process->getOutput() . $process->getErrorOutput();

        $this->output->write("\n" . $output);

        if ($this->input->getOption('stop-on-failure') !== false) {
            $this->poll->stop();
        }

        $this->failed[] = $taskTested;
    }

    /**
     * @return Task[]
     */
    private function findScenarios(InputInterface $input)
    {
        $path = $input->hasArgument('path') ? $input->getArgument('path') : null;

        return $this->specificationFinder->findScenarios($path);
    }
}
