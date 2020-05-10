<?php

namespace DMarynicz\BehatParallelExtension\Cli;

use Behat\Gherkin\Node\ScenarioLikeInterface;
use Behat\Testwork\Cli\Controller;
use Behat\Testwork\Tester\Cli\ExerciseController;
use DMarynicz\BehatParallelExtension\Event\AfterTaskTested;
use DMarynicz\BehatParallelExtension\Event\BeforeTaskTested;
use DMarynicz\BehatParallelExtension\Event\EventDispatcherDecorator;
use DMarynicz\BehatParallelExtension\Event\ParallelTestCompleted;
use DMarynicz\BehatParallelExtension\Exception\UnexpectedValue;
use DMarynicz\BehatParallelExtension\Task\Queue;
use DMarynicz\BehatParallelExtension\Task\TaskEntity;
use DMarynicz\BehatParallelExtension\Task\TaskFactory;
use DMarynicz\BehatParallelExtension\Worker\Poll;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

abstract class ParallelController
{
    /** @var Controller|ExerciseController */
    protected $decoratedController;

    /** @var TaskFactory */
    protected $taskFactory;

    /** @var Queue */
    protected $queue;

    /** @var Poll */
    protected $poll;

    /** @var EventDispatcherDecorator */
    protected $eventDispatcher;

    /** @var int */
    protected $exitCode = 0;

    /** @var ProgressBar */
    protected $progressBar;

    /** @var OutputInterface */
    protected $output;

    /** @var InputInterface */
    protected $input;

    public function __construct(
        Controller $decoratedController,
        TaskFactory $taskFactory,
        Poll $poll,
        Queue $queue,
        EventDispatcherDecorator $eventDispatcher
    ) {
        $this->decoratedController = $decoratedController;
        $this->taskFactory         = $taskFactory;
        $this->queue               = $queue;
        $this->poll                = $poll;
        $this->eventDispatcher     = $eventDispatcher;
    }

    /**
     * @return int|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $startInParallel = $this->getParallelOption($input) !== false;
        if (! $startInParallel) {
            return $this->decoratedController->execute($input, $output);
        }

        $this->output = $output;
        $this->input  = $input;

        $this->eventDispatcher->addListener(BeforeTaskTested::BEFORE, [$this, 'beforeTaskTested']);
        $this->eventDispatcher->addListener(AfterTaskTested::AFTER, [$this, 'afterTaskTested']);

        $tasks = $this->createTasks($input);
        foreach ($tasks as $task) {
            $this->queue->dispatch($task);
        }

        $this->progressBar = new ProgressBar($output, count($tasks));
        ProgressBar::setFormatDefinition(
            'custom',
            " %feature%\n  %scenario%\n %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%"
        );

        $this->progressBar->setMessage('<info>Starting</info>', 'feature');
        $this->progressBar->setMessage('', 'scenario');
        $this->progressBar->setFormat('custom');

        $poolSize = $this->getMaxPoolSize($input);
        $output->writeln(sprintf('Starting parallel scenario tests with %d workers', $poolSize));
        $this->poll->setMaxWorkers($poolSize);
        $this->poll->start();
        $maxSize = $this->getMaxSizeFromParallelOption($input);
        if ($maxSize > 0 && $this->poll->getTotalWorkers() !== $maxSize) {
            $output->writeln(
                sprintf(
                    '<comment>Started poll with only %d workers</comment>',
                    $this->poll->getTotalWorkers()
                )
            );
        }

        $this->progressBar->start();
        $this->poll->wait();
        $output->writeln('');

        $this->eventDispatcher->dispatch(new ParallelTestCompleted(), ParallelTestCompleted::COMPLETED);

        return $this->exitCode;
    }

    public function beforeTaskTested(BeforeTaskTested $beforeTaskTested)
    {
        $task          = $beforeTaskTested->getTask();
        $featureTitle  = sprintf('<info>Feature: %s</info>', $task->getFeature()->getTitle());
        $scenarioTitle = '';
        if ($task->getScenario() instanceof ScenarioLikeInterface) {
            $scenarioTitle = sprintf('<info>Scenario: %s</info>', $task->getScenario()->getTitle());
        }

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

        $this->exitCode = 1;
        $output         = $process->getOutput() . $process->getErrorOutput();

        $this->output->write("\n" . $output);

        if ($this->input->getOption('stop-on-failure') === false) {
            return;
        }

        $this->poll->stop();
    }

    /**
     * @return bool|string|string[]|null
     */
    abstract protected function getParallelOption(InputInterface $input);

    /**
     * @return TaskEntity[]
     */
    private function createTasks(InputInterface $input)
    {
        $path = $input->hasArgument('path') ? $input->getArgument('path') : null;
        if (! is_string($path) && $path !== null) {
            throw new UnexpectedValue('Expected string or null');
        }

        return $this->taskFactory->createTasks($input, $path);
    }

    /**
     * @return int
     */
    private function getNumberOfProcessingUnitsAvailable()
    {
        $nproc = new Process(['nproc']);
        $nproc->run();
        if (! $nproc->isSuccessful()) {
            return 1;
        }

        return (int) trim($nproc->getOutput());
    }

    /**
     * @return int
     */
    private function getMaxPoolSize(InputInterface $input)
    {
        $maxSize = $this->getMaxSizeFromParallelOption($input);
        $maxSize = $maxSize > 0 ? $maxSize : $this->getNumberOfProcessingUnitsAvailable();

        return $maxSize;
    }

    /**
     * @return int
     */
    private function getMaxSizeFromParallelOption(InputInterface $input)
    {
        $option = $this->getParallelOption($input);

        return is_array($option) ?
            (int) $option[0] :
            (int) $option;
    }
}
