<?php
 
namespace Trax\Framework\Service\Event;

abstract class EventListener
{
    /**
     * Event name.
     *
     * @return string
     */
    abstract public function eventClass(): string;

    /**
     * Handle the event remotely.
     *
     * @return  string
     */
    public function eventName(): string
    {
        $eventClass = $this->eventClass();
        return (new $eventClass)->name();
    }

    /**
     * Handle the event locally.
     *
     * @param  \Trax\Framework\Service\Event\Event  $event
     * @return void
     */
    public function handle(Event $event)
    {
        $this->processEvent($event);
    }

    /**
     * Process the event.
     *
     * @param  \Trax\Framework\Service\Event\Event  $event
     * @return void
     */
    abstract protected function processEvent(Event $event);
}
