<?php

namespace Trax\Framework\Console;

use Trax\Framework\Service\Facades\EventManager;
use Trax\Framework\Console\WorkersCommand;
use Trax\Framework\Logging\Logger;
use Trax\Framework\Context;
use Trax\Framework\Service\Event\Redis\RedisDatabase;

abstract class ListenCommand extends WorkersCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service:listen {--daemon} {--stop} {--status} {--workers=} {--keep-alive}';

    /**
     * The streams listeners.
     *
     * @var array
     */
    protected $listeners = [];

    /**
     * The number of Redis attempts before exiting the listener.
     *
     * @var int
     */
    protected $attempts = 20;

    /**
     * Return the name of the service.
     *
     * @return string
     */
    abstract protected function service(): string;

    /**
     * Run the command.
     *
     * @return void
     */
    protected function handleNow(): void
    {
        $this->line('Listener started');

        $this->registerListeners();
        
        if (!$this->waitForRedis()) {
            $this->error('Redis is not available.');
            $this->line('Listener stopped.');
            $this->line('');
            return;
        }
        $this->listen();
    }

    /**
     * Register the event listeners.
     *
     * @return void
     */
    protected function registerListeners(): void
    {
        foreach ($this->listeners as $stream => $listeners) {
            foreach ($listeners as $listener) {
                EventManager::registerStreamListener($stream, $listener);
            }
        }
    }

    /**
     * Wait for Redis.
     *
     * @return bool
     */
    protected function waitForRedis(): bool
    {
        for ($i = 0; $i < $this->attempts; $i++) {
            $this->line('Waiting for Redis...');
            sleep(3);
            if (RedisDatabase::status()->ready) {
                return true;
            }
        }
        return false;
    }

    /**
     * Listen some streams.
     *
     * @return void
     */
    protected function listen(): void
    {
        // Log.
        $this->line('Waiting for stream events...');
        $this->line('');
        
        // Set the context.
        Context::setEntryPoint($this->service());

        Logger::stream(
            'listener_started',
            $this->name
        );

        $streams = array_keys($this->listeners);
        try {
            EventManager::listen($streams, $this, $this->name);
            $this->error('Nothing to listen. The event stream is disabled.');
        } catch (\Throwable $e) {
            $this->error('Listener error: ' . $e->getMessage());
            $this->line('');
        
            Logger::stream(
                'listener_failed',
                $this->name,
                null,
                $e
            );
        }

        // Ignore the error and continue.
        if ($this->option('keep-alive')) {
            $this->info("We ignore the error and continue (option '--keep-alive').");
            $this->line('');
            $this->listen($streams);
        }

        // Stop now.
        $this->line('Listener stopped.');

        Logger::stream(
            'listener_stopped',
            $this->name
        );
    }
}
