<?php
 
namespace Trax\Agents\Listeners;

use Trax\Framework\Service\Event\EventListener;
use Trax\Framework\Service\Event\Event;
use Trax\Statements\Events\StatementsRecorded;
use Trax\Agents\Recording\AgentUpdater;

class StatementsRecordedEventListener extends EventListener
{
    /**
     * Event name.
     *
     * @return string
     */
    public function eventClass(): string
    {
        return StatementsRecorded::class;
    }
 
    /**
     * Process the event.
     *
     * @param  \Trax\Framework\Service\Event\Event  $event
     * @return void
     */
    protected function processEvent(Event $event)
    {
        app(AgentUpdater::class)->update($event->data);
    }
}
