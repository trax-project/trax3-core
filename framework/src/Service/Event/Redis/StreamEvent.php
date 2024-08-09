<?php
 
namespace Trax\Framework\Service\Event\Redis;
 
use Trax\Framework\Service\Event\StreamEvent as BaseStreamEvent;
use Prwnr\Streamer\EventDispatcher\ReceivedMessage;

class StreamEvent extends BaseStreamEvent
{
    /**
     * Create a new event.
     *
     * @param  ReceivedMessage  $message
     * @return void
     */
    public function __construct(ReceivedMessage $message)
    {
        $content = $message->getData();
        $event = (object)$content['event'];
        $this->stream = $event->stream;
        $this->name = $event->name;
        $this->class = $event->class;
        $this->data = $content['data'];
        $this->context = $content['context'];
    }
}
