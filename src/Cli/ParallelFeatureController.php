<?php

namespace DMarynicz\BehatParallelExtension\Cli;

use Behat\Testwork\Cli\Controller;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

final class ParallelFeatureController extends ParallelController implements Controller
{
    public function configure(SymfonyCommand $command): void
    {
        $this->decoratedController->configure($command);

        $command->addOption(
            'parallel-feature',
            null,
            InputOption::VALUE_OPTIONAL,
            'How many scenario jobs run in parallel feature mode? Available values empty or integer',
            false
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function getParallelOption(InputInterface $input)
    {
        return $input->getOption('parallel-feature');
    }
}
