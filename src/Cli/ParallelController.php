<?php

namespace DMarynicz\BehatParallelExtension\Cli;

use Behat\Testwork\Cli\Controller;
use Behat\Testwork\Tester\Cli\ExerciseController;
use DMarynicz\BehatParallelExtension\Event\AfterTaskTested;
use DMarynicz\BehatParallelExtension\Event\BeforeTaskTested;
use DMarynicz\BehatParallelExtension\Event\EventDispatcherDecorator;
use DMarynicz\BehatParallelExtension\Event\ParallelTestsAborted;
use DMarynicz\BehatParallelExtension\Event\ParallelTestsCompleted;
use DMarynicz\BehatParallelExtension\Exception\UnexpectedValue;
use DMarynicz\BehatParallelExtension\Task\Queue;
use DMarynicz\BehatParallelExtension\Task\TaskEntity;
use DMarynicz\BehatParallelExtension\Task\TaskFactory;
use DMarynicz\BehatParallelExtension\Util\CanDetermineNumberOfProcessingUnits;
use DMarynicz\BehatParallelExtension\Worker\Poll;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

    /** @var CanDetermineNumberOfProcessingUnits */
    protected $numberOfCores;

    /** @var TitleListFormatter */
    protected $titleListFormatter;

    public function __construct(
        Controller $decoratedController,
        TaskFactory $taskFactory,
        Poll $poll,
        Queue $queue,
        EventDispatcherDecorator $eventDispatcher,
        CanDetermineNumberOfProcessingUnits $numberOfCores
    ) {
        $this->decoratedController = $decoratedController;
        $this->taskFactory         = $taskFactory;
        $this->queue               = $queue;
        $this->poll                = $poll;
        $this->eventDispatcher     = $eventDispatcher;
        $this->numberOfCores       = $numberOfCores;
    }

    /**
     * @return bool|string|string[]|null
     */
    abstract protected function getParallelOption(InputInterface $input);

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

        // Unable to get the COLUMNS environment variable to know the width of the terminal
        // because Behat overrides it to 9999. Let's use the default TitleListFormatter's maxLength value.
        $this->titleListFormatter = new TitleListFormatter();

        return $this->parallelExecute();
    }

    public function beforeTaskTested(BeforeTaskTested $beforeTaskTested): void
    {
        $task  = $beforeTaskTested->getTask();
        $units = $task->getUnits();

        $this->progressBar->setMessage($this->titleListFormatter->formatFeatures($units), 'feature');
        $this->progressBar->setMessage($this->titleListFormatter->formatScenarios($units), 'scenario');
    }

    public function afterTaskTested(AfterTaskTested $taskTested): void
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

    public function parallelTestsAborted(): void
    {
        $this->poll->stop();
    }

    private function parallelExecute(): int
    {
        $this->addEventListeners();
        $this->setupTasksWithProgressBar();
        $this->startPoll();
        $this->progressBar->start();
        $this->poll->wait();
        $this->output->writeln('');

        $this->eventDispatcher->dispatch(new ParallelTestsCompleted(), ParallelTestsCompleted::COMPLETED);

        return $this->exitCode;
    }

    private function addEventListeners(): void
    {
        $this->eventDispatcher->addListener(BeforeTaskTested::BEFORE, [$this, 'beforeTaskTested']);
        $this->eventDispatcher->addListener(AfterTaskTested::AFTER, [$this, 'afterTaskTested']);
        $this->eventDispatcher->addListener(ParallelTestsAborted::ABORTED, [$this, 'parallelTestsAborted']);
    }

    private function setupTasksWithProgressBar(): void
    {
        $tasks = $this->createTasks();
        foreach ($tasks as $task) {
            $this->queue->dispatch($task);
        }

        $this->progressBar = new ProgressBar($this->output, count($tasks));
        ProgressBar::setFormatDefinition(
            'custom',
            " %feature%\n  %scenario%\n %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%"
        );

        $this->progressBar->setMessage('<info>Starting</info>', 'feature');
        $this->progressBar->setMessage('', 'scenario');
        $this->progressBar->setFormat('custom');
    }

    private function startPoll(): void
    {
        $poolSize  = $this->getMaxPoolSize();
        $chunkSize = (int) $this->input->getOption('parallel-chunk-size');
        $message   = sprintf(
            'Starting parallel tests with %d workers',
            $poolSize
        );
        if ($chunkSize > 1) {
            $message .= sprintf(' and %d tests per worker', $chunkSize);
        }

        $this->output->writeln($message);
        $this->poll->setMaxWorkers($poolSize);
        $this->poll->start();
        $maxSize = $this->getMaxSizeFromParallelOption();
        if ($maxSize <= 0 || $this->poll->getTotalWorkers() === $maxSize) {
            return;
        }

        $this->output->writeln(
            sprintf(
                '<comment>Started poll with only %d workers</comment>',
                $this->poll->getTotalWorkers()
            )
        );
    }

    /**
     * @return TaskEntity[]
     */
    private function createTasks()
    {
        $paths = $this->input->hasArgument('paths') ? $this->input->getArgument('paths') : null;

        if ($paths === null || is_string($paths)) {
            return $this->taskFactory->createTasks($this->input, $paths);
        }

        if (! is_array($paths)) {
            throw new UnexpectedValue('Expected array, string or null');
        }

        if (empty($paths)) {
            return $this->taskFactory->createTasks($this->input, null);
        }

        $tasksPerPath = [];
        foreach ($paths as $path) {
            $tasksPerPath[] = $this->taskFactory->createTasks($this->input, $path);
        }

        return array_merge(...$tasksPerPath);
    }

    /**
     * @return int
     */
    private function getNumberOfProcessingUnitsAvailable()
    {
        return $this->numberOfCores->getNumberOfProcessingUnits();
    }

    /**
     * @return int
     */
    private function getMaxPoolSize()
    {
        $maxSize = $this->getMaxSizeFromParallelOption();
        $maxSize = $maxSize > 0 ? $maxSize : $this->getNumberOfProcessingUnitsAvailable();

        return $maxSize;
    }

    /**
     * @return int
     */
    private function getMaxSizeFromParallelOption()
    {
        $option = $this->getParallelOption($this->input);

        return is_array($option) ?
            (int) $option[0] :
            (int) $option;
    }
}
