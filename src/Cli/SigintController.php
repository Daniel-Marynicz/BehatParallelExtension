<?php

namespace DMarynicz\BehatParallelExtension\Cli;

use Behat\Testwork\Cli\Controller;
use DMarynicz\BehatParallelExtension\Event\EventDispatcherDecorator;
use DMarynicz\BehatParallelExtension\Event\ParallelTestsAborted;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SigintController implements Controller
{
    /** @var EventDispatcherDecorator */
    private $eventDispatcher;

    public function __construct(EventDispatcherDecorator $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function configure(SymfonyCommand $command): void
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

        if ($this->isSystemSupportsPcntl()) {
            declare(ticks=1);
            pcntl_signal(SIGINT, [$this, 'abortTests']);
        }

        return null;
    }

    /**
     * Dispatches AFTER exercise event and exits program.
     */
    public function abortTests(): void
    {
        $this->eventDispatcher->dispatch(new ParallelTestsAborted(), ParallelTestsAborted::ABORTED);
    }

    private function isParallelModeEnabled(InputInterface $input): bool
    {
        return $input->getOption('parallel') !== false || $input->getOption('parallel-feature') !== false;
    }

    /**
     * Windows system's does not have pcntl extension
     */
    private function isSystemSupportsPcntl(): bool
    {
        return function_exists('pcntl_signal');
    }
}
