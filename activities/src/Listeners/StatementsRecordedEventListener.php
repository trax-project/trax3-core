<?php
 
namespace Trax\Activities\Listeners;

use Trax\Framework\Service\Event\EventListener;
use Trax\Framework\Service\Event\Event;
use Trax\Statements\Events\StatementsRecorded;
use Trax\Activities\Recording\ActivityUpdater;

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
        app(ActivityUpdater::class)->update($event->data);
    }
}
