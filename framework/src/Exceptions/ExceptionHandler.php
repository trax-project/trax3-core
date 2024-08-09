<?php

namespace Trax\Framework\Exceptions;

use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Trax\Framework\Exceptions\SimpleException;
use Trax\Framework\Xapi\Exceptions\XapiExceptionHandler;
use Trax\Framework\Logging\Logger;

class ExceptionHandler extends XapiExceptionHandler
{
    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        /*
        $this->reportable(function (Throwable $e) {
            Logger::exception($e);
        });
        */
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Simple exception.
        if ($exception instanceof SimpleException) {
            Logger::http(400, $exception->getMessage());
            return response($exception->getMessage(), 400);
        }

        // Auth.
        if ($exception instanceof AuthenticationException) {
            // Should log something here !!!
            return parent::render($request, $exception);
        }
        if ($exception instanceof AuthorizationException) {
            // Should log something here !!!
            return response($exception->getMessage(), 403);
        }

        // Other, including xAPI.
        return parent::render($request, $exception);
    }
}
