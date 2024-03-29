<?php

namespace DMarynicz\BehatParallelExtension\Cli;

use Behat\Behat\Snippet\SnippetRepository;
use Behat\Testwork\Cli\Controller;
use Behat\Testwork\Tester\Cli\ExerciseController;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class SnippetsController implements Controller
{
    /** @var Controller|ExerciseController */
    private $decoratedController;

    /** @var SnippetRepository */
    private $registry;

    /**
     * @param Controller|ExerciseController $decoratedController
     */
    public function __construct(Controller $decoratedController, SnippetRepository $registry)
    {
        $this->decoratedController = $decoratedController;
        $this->registry            = $registry;
    }

    public function configure(SymfonyCommand $command): void
    {
        $this->decoratedController->configure($command);
        $command->addOption(
            '--fail-on-undefined-step',
            null,
            InputOption::VALUE_NONE,
            'Fail tests on undefined steps.'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $result = $this->decoratedController->execute($input, $output);

        $failOnUndefinedSteps = $input->getOption('fail-on-undefined-step') !== false;

        if ($result === 0 && $failOnUndefinedSteps && $this->hasUndefinedStepsOrSnippets()) {
            $output->writeln('<error>Tests has undefined steps!</error>');

            return 1;
        }

        return $result;
    }

    private function hasUndefinedStepsOrSnippets(): bool
    {
        $undefined = $this->registry->getUndefinedSteps();
        $snippets  = $this->registry->getSnippets();

        return count($undefined) > 0 || count($snippets) > 0;
    }
}
