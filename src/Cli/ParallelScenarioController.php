<?php

namespace DMarynicz\BehatParallelExtension\Cli;

use Behat\Testwork\Cli\Controller;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

final class ParallelScenarioController extends ParallelController implements Controller
{
    public function configure(SymfonyCommand $command): void
    {
        $this->decoratedController->configure($command);

        $command->addOption(
            'parallel',
            'l',
            InputOption::VALUE_OPTIONAL,
            'How many scenario jobs run in parallel? Available values empty or integer',
            false
        )
            ->addUsage('--parallel 8')
            ->addUsage('--parallel');
        $command->addOption(
            'parallel-chunk-size',
            null,
            InputOption::VALUE_OPTIONAL,
            'How many scenarios run in one chunk? (Behat >= 3.23)',
            1
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function getParallelOption(InputInterface $input)
    {
        return $input->getOption('parallel');
    }
}
