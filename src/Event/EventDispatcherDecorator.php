<?php

namespace DMarynicz\BehatParallelExtension\Event;

use Behat\Testwork\EventDispatcher\TestworkEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventDispatcherDecorator
{
    /** @var EventDispatcher */
    private $eventDispatcher;

    public function __construct(TestworkEventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Dispatches an event to all registered listeners.
     *
     * @param object      $event     The event to pass to the event handlers/listeners
     * @param string|null $eventName The name of the event to dispatch. If not supplied,
     *                               the class of $event should be used instead.
     *
     * @return object The passed $event MUST be returned
     */
    public function dispatch($event, $eventName = null)
    {
        $dispatcherVersion = defined(TestworkEventDispatcher::class . '::DISPATCHER_VERSION')
            ? constant(TestworkEventDispatcher::class . '::DISPATCHER_VERSION')
            : 1;
        if ((int) $dispatcherVersion === 2) {
            return $this->eventDispatcher->dispatch($event, $eventName);
        }

        // @phpstan-ignore-next-line
        return $this->eventDispatcher->dispatch($eventName, $event);
    }

    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string   $eventName
     * @param callable $listener
     * @param int      $priority,
     *
     * @return void
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->eventDispatcher->addListener($eventName, $listener, $priority);
    }
}
