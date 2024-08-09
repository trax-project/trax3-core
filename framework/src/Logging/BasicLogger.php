<?php

namespace Trax\Framework\Logging;

use Throwable;
use Trax\Framework\Logging\LoggerContract;

class BasicLogger implements LoggerContract
{
    /**
     * Log an uncatched exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function exception(Throwable $exception): void
    {
        // Extended Edition only.
    }

    /**
     * Log an xAPI request.
     *
     * @param  int  $status     HTTP response status, determines the event (passed or failed)
     * @param  array  $data
     * @return void
     */
    public function xapi(int $status, array $data = []): void
    {
        // Extended Edition only.
    }

    /**
     * Log an HTTP request, be it an API call from the external or from the UI.
     *
     * @param  int  $status     HTTP response status, determines the event (passed or failed)
     * @param  string  $phrase
     * @param  \Throwable  $exception
     * @return void
     */
    public function http(int $status, string $phrase = null, Throwable $exception = null): void
    {
        // Extended Edition only.
    }

    /**
     * Log an event coming from the execution of a console command.
     *
     * @param  string  $event  started, passed, failed
     * @param  string  $command
     * @param  array  $settings
     * @param  string  $feedback
     * @param  array  $jobData
     * @param  \Throwable  $exception
     * @return void
     */
    public function console(string $event, string $command, array $settings = [], string $feedback = null, array $jobData = null, Throwable $exception = null): void
    {
        // Extended Edition only.
    }

    /**
     * Log something related to the event stream.
     *
     * @param  string  $event  listener_started, listener_failed, listener_stopped, event_failed
     * @param  string  $listener
     * @param  string  $streamEvent
     * @param  \Throwable  $exception
     * @return void
     */
    public function stream(string $event, string $listener, string $streamEvent = null, Throwable $exception = null): void
    {
        // Extended Edition only.
    }

    /**
     * Log authentication and authorization events.
     *
     * @param  string  $event  logged_in, logged_out, authentication_failed, authorization_failed
     * @param  array  $data
     * @return void
     */
    public function auth(string $event, array $data): void
    {
        // Extended Edition only.
    }
}
