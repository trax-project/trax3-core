<?php

namespace Trax\Framework\Service\Event\Redis;

use Illuminate\Console\Command;
use Trax\Framework\Service\Event\Streamer as StreamerContract;
use Trax\Framework\Service\Event\Event;
use Trax\Framework\Logging\Logger;
use Prwnr\Streamer\EventDispatcher\Streamer as PrwnrStreamer;
use Prwnr\Streamer\EventDispatcher\ReceivedMessage;
use Prwnr\Streamer\ListenersStack;
use Prwnr\Streamer\Stream;

/**
 * Class Streamer.
 */
class Streamer implements StreamerContract
{
    /**
     * @var \Prwnr\Streamer\EventDispatcher\Streamer
     */
    protected $streamer;

    /**
     * Create a streamer instance.
     *
     * @return \Trax\Framework\Service\Event\Redis\Streamer
     */
    public function __construct()
    {
        // Workaround for https://github.com/prwnr/laravel-streamer/issues/17
        ini_set("default_socket_timeout", -1);

        $this->streamer = app(PrwnrStreamer::class);
    }
    
    /**
     * Register a listener.
     *
     * @param  string  $streamName
     * @param  string  $listenerClass
     * @return void
     */
    public function registerListener(string $streamName, string $listenerClass)
    {
        ListenersStack::add($streamName, $listenerClass);
    }

    /**
     * Dispatch a TRAX event on the stream.
     *
     * @var \Trax\Framework\Service\Event\Event  $event
     * @return void
     */
    public function dispatch(Event $event): void
    {
        $this->streamer->emit(new PrwnrEvent($event));
    }

    /**
     * Get the stream length.
     *
     * @param  string  $streamName
     * @return int
     */
    public function streamLength(string $streamName): int
    {
        return (new Stream($streamName))->len();
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
        // Define the group.
        if (isset($group)) {
            $consumer = $group . '-' . rand();
            $this->streamer->asConsumer($consumer, $group);
        }

        // Then, listen.
        $this->streamer->listen($streams, function (ReceivedMessage $message) use ($command) {
            $event = new StreamEvent($message);

            foreach (ListenersStack::getListenersFor($event->stream) as $streamListenerClass) {
                $listener = new $streamListenerClass;
                try {
                    $command->info("'$event->name' event received");
                    $listener->handle($event, $command);
                } catch (\Throwable $e) {
                    $command->error("error processing '$event->name' event > " . $e->getMessage());
                    
                    Logger::stream(
                        'event_failed',
                        $command->getName(),
                        $event->name,
                        $e
                    );
                }
            }
        });
    }
}
