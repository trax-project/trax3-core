<?php

namespace Trax\Framework\Service\Event;

use Illuminate\Console\Command;
use Trax\Framework\Service\Event\Event;

/**
 * Class Streamer.
 */
class DefaultStreamer implements Streamer
{
    /**
     * Register a listener.
     *
     * @param  string  $streamName
     * @param  string  $listenerClass
     * @return void
     */
    public function registerListener(string $streamName, string $listenerClass)
    {
    }

    /**
     * Dispatch a TRAX event on the stream.
     *
     * @var \Trax\Framework\Service\Event\Event  $event
     * @return void
     */
    public function dispatch(Event $event): void
    {
    }

    /**
     * Get the stream length.
     *
     * @param  string  $streamName
     * @return int
     */
    public function streamLength(string $streamName): int
    {
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
    }
}
