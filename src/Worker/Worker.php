<?php


namespace DMarynicz\BehatParallelExtension\Worker;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Behat\Tester\Result\FailedStepSearchResult;
use Behat\Testwork\Environment\StaticEnvironment;
use Behat\Testwork\EventDispatcher\Event\AfterExerciseCompleted;
use Behat\Testwork\EventDispatcher\Event\BeforeExerciseCompleted;
use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use Behat\Testwork\Tester\Setup\SuccessfulTeardown;
use DMarynicz\BehatParallelExtension\Event\AfterTaskTested;
use DMarynicz\BehatParallelExtension\Event\BeforeTaskTested;
use DMarynicz\BehatParallelExtension\Exception\RuntimeException;
use DMarynicz\BehatParallelExtension\Queue\Queue;
use DMarynicz\BehatParallelExtension\Queue\ScenarioTask;
use DMarynicz\BehatParallelExtension\Queue\Task;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;
use Behat\Testwork\Tester\Result\TestResults;
use Behat\Testwork\Tester\Result\TestWithSetupResult;
use Behat\Testwork\Tester\Result\IntegerTestResult;
use Behat\Testwork\Tester\Result\TestResult;


class Worker
{
    /**
     * @var array
     */
    private $env;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var bool
     */
    private $started = false;

    /**
     * @var Process
     */
    private $currentProcess;

    /**
     * @var Task
     */
    private $currentTask;

    /**
     * @param Queue $queue
     * @param array $env
     */
    public function __construct(Queue $queue, $env, EventDispatcherInterface $eventDispatcher)
    {
        $this->env = $env;
        $this->queue = $queue;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function start()
    {
        if ($this->isStarted()) {
            throw new RuntimeException('Worker is already started');
        }
        $this->started = true;
        $this->next();
    }

    private function next()
    {
        $this->currentTask = $this->queue->dequeue();
        $before = new BeforeTaskTested($this->currentTask);
        $this->eventDispatcher->dispatch($before, BeforeTaskTested::BEFORE);
        $this->currentProcess= new Process(
            $this->currentTask->getCommand(),
            null,
            $this->env,
            null,
            120
        );
        $this->currentProcess->setPty(true);
        $this->currentProcess->start();
    }

    public function wait()
    {
        if ($this->isRunning()) {
            return;
        }

        if ($this->currentTask) {
            $tested = new AfterTaskTested($this->currentTask, $this->currentProcess);
            $this->eventDispatcher->dispatch($tested, AfterTaskTested::AFTER);
            $this->clearCurrent();
        }



        if ($this->queue->isEmpty()) {
            $this->started = false;
            return;
        }

        $this->next();
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        return $this->currentProcess && $this->currentProcess->isRunning();
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        return $this->started;
    }

    public function stop()
    {
        $this->started = false;
        $this->currentProcess->stop(0);
    }

    private function clearCurrent()
    {
        $this->currentTask = null;
        $this->currentProcess = null;
    }
}

