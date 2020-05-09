<?php

namespace DMarynicz\BehatParallelExtension\Cli;

use Behat\Testwork\Cli\Controller;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

final class ParallelScenarioController extends ParallelController implements Controller
{
    /**
     * {@inheritDoc}
     */
    public function configure(SymfonyCommand $command)
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
    }

    /**
     * {@inheritDoc}
     */
    protected function getParallelOption(InputInterface $input)
    {
        return $input->getOption('parallel');
    }
}
