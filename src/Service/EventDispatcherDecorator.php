<?php


namespace DMarynicz\BehatParallelExtension\Service;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as ContractsEventDispatcherInterface;
use Behat\Testwork\EventDispatcher\TestworkEventDispatcher;

class EventDispatcherDecorator
{
    /**
     * @var EventDispatcher
     */
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
        return $this->eventDispatcher->dispatch($event, $eventName);
//        if ($this->eventDispatcher instanceof ContractsEventDispatcherInterface) {
//            return $this->eventDispatcher->dispatch($event, $eventName);
//        }

        return $this->eventDispatcher->dispatch($eventName, $event);
    }

    /**
     * @param string $eventName
     * @param $listener
     * @param int $priority,
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        return $this->eventDispatcher->addListener($eventName, $listener, $priority);
    }
}
