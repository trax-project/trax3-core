<?php
 
 namespace Trax\Framework\Service\Event;
 
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Trax\Framework\Context;

abstract class Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var mixed
     */
    public $data;

    /**
     * @var array
     */
    public $context;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $data
     * @return void
     */
    public function __construct($data = null)
    {
        $this->data = $data;
        $this->context = Context::serialize();
    }
 
    /**
     * Event stream.
     *
     * @return string
     */
    abstract public function stream(): string;

    /**
     * Event name.
     *
     * @return string
     */
    abstract public function name(): string;
}
