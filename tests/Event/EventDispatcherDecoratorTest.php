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

    public function testDispatch(): void
    {
        $dispatcher = new TestworkEventDispatcher();
        $decorator  = new EventDispatcherDecorator($dispatcher);
        $event      = $this->createMock(Event::class);
        $returned   = $decorator->dispatch($event, 'some-name');

        $this->assertSame($event, $returned);
    }

    public function testAddListener(): void
    {
        $dispatcher = new TestworkEventDispatcher();
        $decorator  = new EventDispatcherDecorator($dispatcher);
        $called     = false;
        $decorator->addListener('some-name', static function () use (&$called) {
            $called = true;
        }, 123);
        $decorator->dispatch($this->createMock(Event::class), 'some-name');

        $this->assertTrue($called);
    }
}
