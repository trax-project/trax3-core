<?php
 
 namespace Trax\Framework\Service\Event;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event as LaravelEvent;
use Trax\Framework\Service\Event\Streamer;
use Trax\Framework\Service\Config;

class EventManager
{
    /**
     * @var \Trax\Framework\Service\Event\Streamer
     */
    protected $streamer;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->streamer = app(Streamer::class);
    }

    /**
     * Dispatch the event everywhere.
     *
     * @param  string  $eventClass
     * @param  mixed  $data
     * @return void
     */
    public function dispatch(string $eventClass, $data = null)
    {
        $this->dispatchLocally($eventClass, $data);
        $this->dispatchOnStream($eventClass, $data);
    }

    /**
     * Dispatch the event locally.
     *
     * @param  string  $eventClass
     * @param  mixed  $data
     * @return void
     */
    public function dispatchLocally(string $eventClass, $data = null)
    {
        $eventClass::dispatch($data);
    }

    /**
     * Dispatch the event on the stream.
     *
     * @param  string  $eventClass
     * @param  mixed  $data
     * @return void
     */
    public function dispatchOnStream(string $eventClass, $data = null)
    {
        if (!Config::streamEnabled()) {
            return;
        }
        $event = new $eventClass($data);
        $this->streamer->dispatch($event);
    }

    /**
     * Register a local event.
     *
     * @param  string  $eventClass
     * @param  string  $listenerClass
     * @return void
     */
    public function registerEventListener(string $eventClass, string $listenerClass)
    {
        LaravelEvent::listen($eventClass, $listenerClass);
    }

    /**
     * Register a stream listener.
     *
     * @param  string  $streamName
     * @param  string  $listenerClass
     * @return void
     */
    public function registerStreamListener(string $streamName, string $listenerClass)
    {
        if (!Config::streamEnabled()) {
            return;
        }
        $this->streamer->registerListener($streamName, $listenerClass);
    }

    /**
     * Get the stream length.
     *
     * @param  string  $eventName
     * @return int
     */
    public function streamLength(string $eventName): int
    {
        if (!Config::streamEnabled()) {
            return 0;
        }
        return $this->streamer->streamLength($eventName);
    }

    /**
     * Listen to streams.
     *
     * @param  array  $streams
     * @param  \Illuminate\Console\Command  $command
     * @param  string  $group
     * @return void
     */
    public function listen(array $streams, Command $command, string $group = null): void
    {
        if (!Config::streamEnabled()) {
            return;
        }
        $this->streamer->listen(
            $streams,
            $command,
            $group
        );
    }
}
