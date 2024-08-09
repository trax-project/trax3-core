<?php
 
namespace Trax\Framework\Service\Event;

use Illuminate\Console\Command;
use Trax\Framework\Context;
use Trax\Framework\Service\Event\StreamEvent;

class StreamListener
{
    /**
     * The service.
     *
     * @var string
     */
    protected $service = 'dev';

    /**
     * The events to dispatch locally.
     * An empty list means that all the events are dispatched.
     *
     * @var array
     */
    protected $accept = [];

    /**
     * Handle the event.
     *
     * @param  \Trax\Framework\Service\Event\StreamEvent  $event
     * @param  \Illuminate\Console\Command  $command
     * @return void
     */
    public function handle(StreamEvent $event, Command $command): void
    {
        if (empty($this->accept) || in_array($event->name, $this->accept)) {

            // Restore the context. We get the data partition, pipeline and consumer.
            Context::unserialize($event->context);

            // Dispatch the event locally.
            $eventClass = $event->class;
            $eventClass::dispatch($event->data);
        }
    }
}
