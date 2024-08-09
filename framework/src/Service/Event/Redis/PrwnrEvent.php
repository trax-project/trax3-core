<?php
 
 namespace Trax\Framework\Service\Event\Redis;
 
use Trax\Framework\Service\Event\Event;
use Prwnr\Streamer\Contracts\Event as PrwnrEventContract;

class PrwnrEvent implements PrwnrEventContract
{
    /**
     * @var \Trax\Framework\Service\Event\Event
     */
    protected $event;

    /**
     * Create a new event instance.
     *
     * @var \Trax\Framework\Service\Event\Event  $event
     * @return void
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }
 
    /**
     * Event name. Can be any string
     * This name will be later used as event name for listening.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->event->stream();
    }

    /**
     * Event type. Can be one of the predefined types from this contract.
     *
     * @return string
     */
    public function type(): string
    {
        return PrwnrEvent::TYPE_EVENT;
    }

    /**
     * Event payload that will be sent as message to Stream.
     *
     * @return array
     */
    public function payload(): array
    {
        return [
            'event' => [
                'class' => get_class($this->event),
                'name' => $this->event->name(),
                'stream' => $this->event->stream(),
            ],
            'data' => $this->event->data,
            'context' => $this->event->context,
        ];
    }
}
