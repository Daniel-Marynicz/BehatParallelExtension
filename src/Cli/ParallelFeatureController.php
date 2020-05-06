<?php

namespace DMarynicz\BehatParallelExtension\Cli;

use Behat\Testwork\Cli\Controller;
use Behat\Testwork\Cli\ServiceContainer\CliExtension;
use Behat\Testwork\Tester\Cli\ExerciseController;
use DMarynicz\BehatParallelExtension\Exception\Logic;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ParallelFeatureController implements Controller
{
    const SERVICE_ID = CliExtension::CONTROLLER_TAG . '.parallel_extension.parallel_feature_exercise';

    /** @var ExerciseController */
    private $decoratedExerciseController;

    public function __construct(
        ExerciseController $decoratedExerciseController
    ) {
        $this->decoratedExerciseController = $decoratedExerciseController;
    }

    public function configure(SymfonyCommand $command)
    {
        $this->decoratedExerciseController->configure($command);

        $command->addOption(
            'parallel-feature',
            null,
            InputOption::VALUE_OPTIONAL,
            'How many feature jobs run in parallel? Available values empty or integer',
            false
        )
            ->addUsage('--parallel-feature 8')
            ->addUsage('--parallel-feature');
    }

    /**
     * @return int|null
     *
     * @TODO
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $startInParallel = $input->getOption('parallel-scenario') !== false;
        if (! $startInParallel) {
            return $this->decoratedExerciseController->execute($input, $output);
        }

        throw new Logic('Not yet implemented');
    }
}
