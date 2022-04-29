<?php

namespace DMarynicz\Tests\Event;

use Behat\Testwork\Event\Event;
use Behat\Testwork\EventDispatcher\TestworkEventDispatcher;
use DG\BypassFinals;
use DMarynicz\BehatParallelExtension\Event\EventDispatcherDecorator;
use PHPUnit\Framework\TestCase;

class EventDispatcherDecoratorTest extends TestCase
{
    protected function setUp(): void
    {
        BypassFinals::enable();
    }

    public function testDispatch()
    {
        $dispatcher = new TestworkEventDispatcher();
        $decorator  = new EventDispatcherDecorator($dispatcher);
        $decorator->dispatch($this->createMock(Event::class), 'some-name');

        $this->assertTrue(true);
    }

    public function testAddListener()
    {
        $dispatcher = new TestworkEventDispatcher();
        $decorator  = new EventDispatcherDecorator($dispatcher);
        $decorator->addListener('some-name', static function () {
        }, 123);

        $this->assertTrue(true);
    }
}
