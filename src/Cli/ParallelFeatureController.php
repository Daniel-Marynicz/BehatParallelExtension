<?php

namespace DMarynicz\BehatParallelExtension\Cli;

use Behat\Testwork\Cli\Controller;
use Behat\Testwork\Cli\ServiceContainer\CliExtension;
use Behat\Testwork\Tester\Cli\ExerciseController;
use DMarynicz\BehatParallelExtension\Service\FeatureSpecificationsFinder;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ParallelFeatureController implements Controller
{
    const SERVICE_ID = CliExtension::CONTROLLER_TAG . '.parallel_extension.parallel_feature_exercise';

    /** @var ExerciseController */
    private $decoratedExerciseController;

    /** @var FeatureSpecificationsFinder */
    private $specificationFinder;

    public function __construct(
        ExerciseController $decoratedExerciseController,
        FeatureSpecificationsFinder $specificationsFinder
    ) {
        $this->decoratedExerciseController = $decoratedExerciseController;
        $this->specificationFinder         = $specificationsFinder;
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
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $startInParallel = $input->getOption('parallel-scenario') !== false;
        if (! $startInParallel) {
            return $this->decoratedExerciseController->execute($input, $output);
        }

        $specs = $this->findSpecifications($input);

        return 0;
    }

    /**
     * @return array|string[]
     */
    private function findSpecifications(InputInterface $input)
    {
        return $this->specificationFinder->findFeatures($input->getArgument('path'));
    }
}
