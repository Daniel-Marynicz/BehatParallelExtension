<?php

namespace DMarynicz\BehatParallelExtension\Cli;

use Behat\Testwork\Cli\ServiceContainer\CliExtension;
use DMarynicz\BehatParallelExtension\Service\ScenarioSpecificationsFinder;
use Behat\Testwork\Cli\Controller;
use Behat\Testwork\Tester\Cli\ExerciseController;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ParallelScenarioController implements Controller
{
    const SERVICE_ID = CliExtension::CONTROLLER_TAG.'.parallel_extension.parallel_scenario_exercise';

    /**
     * @var ExerciseController
     */
    private $decoratedExerciseController;

    /**
     * @var ScenarioSpecificationsFinder
     */
    private $specificationFinder;

    /**
     * @param ExerciseController $decoratedExerciseController
     * @param ScenarioSpecificationsFinder $specificationsFinder
     */
    public function __construct(
        ExerciseController $decoratedExerciseController,
        ScenarioSpecificationsFinder $specificationsFinder
    ) {
        $this->decoratedExerciseController = $decoratedExerciseController;
        $this->specificationFinder = $specificationsFinder;
    }
    public function configure(SymfonyCommand $command)
    {
        $this->decoratedExerciseController->configure($command);

        $command->addOption(
            'parallel-scenario',
            null,
            InputOption::VALUE_OPTIONAL,
            'How many scenario jobs run in parallel? Available values empty or integer',
            false
        )
            ->addUsage('--parallel-scenario 8')
            ->addUsage('--parallel-scenario');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $startInParallel = $input->getOption('parallel-scenario') !== false;
        if (! $startInParallel) {
            return $this->decoratedExerciseController->execute($input, $output);
        }

        $specs = $this->findSpecifications($input);
    }

    /**
     * @param InputInterface $input
     * @return array|string[]
     */
    private function findSpecifications(InputInterface $input)
    {
        return $this->specificationFinder->findScenarios($input->getArgument('path'));
    }
}
