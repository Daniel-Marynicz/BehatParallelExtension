<?php

namespace DMarynicz\BehatParallelExtension\Cli;

use Behat\Testwork\Cli\Controller;
use DMarynicz\BehatParallelExtension\Event\EventDispatcherDecorator;
use DMarynicz\BehatParallelExtension\Event\ParallelTestsAborted;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SigintController implements Controller
{
    /** @var EventDispatcherDecorator */
    private $eventDispatcherDecorator;

    public function __construct(EventDispatcherDecorator $eventDispatcherDecorator)
    {
        $this->eventDispatcherDecorator = $eventDispatcherDecorator;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(SymfonyCommand $command)
    {
    }

    /**
     * @inheritDoc
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (! $this->isParallelModeEnabled($input)) {
            return null;
        }

        declare(ticks = 1);
        pcntl_async_signals(true);
        pcntl_signal(SIGINT, [$this, 'abortTests']);

        return null;
    }

    /**
     * Dispatches AFTER exercise event and exits program.
     */
    public function abortTests()
    {
        $this->eventDispatcherDecorator->dispatch(new ParallelTestsAborted(), ParallelTestsAborted::ABORTED);

        exit(1);
    }

    /**
     * @return bool
     */
    private function isParallelModeEnabled(InputInterface $input)
    {
        return $input->getOption('parallel') !== false || $input->getOption('parallel-feature');
    }
}
