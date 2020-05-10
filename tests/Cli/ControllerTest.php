<?php

namespace DMarynicz\Tests\Cli;

use Behat\Testwork\Cli\Controller;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ControllerTest extends TestCase
{
    /** @var Controller */
    protected $controller;

    /** @var MockObject|Controller */
    protected $decoratedController;

    /** @var MockObject|InputInterface */
    protected $input;

    /** @var MockObject|OutputInterface */
    protected $output;

    /** @var MockObject|SymfonyCommand */
    protected $command;

    protected function setUp()
    {
        $this->decoratedController = $this->createMock(Controller::class);
        $this->input               = $this->createMock(InputInterface::class);
        $this->output              = $this->createMock(OutputInterface::class);
        $this->command             = $this->createMock(SymfonyCommand::class);
    }
}
