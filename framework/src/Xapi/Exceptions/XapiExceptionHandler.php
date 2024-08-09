<?php

namespace Trax\Framework\Xapi\Exceptions;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Foundation\Exceptions\Handler;
use Trax\Framework\Xapi\Exceptions\XapiValidationException;
use Trax\Framework\Logging\Logger;
use Trax\Framework\Exceptions\HttpException;
use Trax\Framework\Exceptions\SimpleException;

class XapiExceptionHandler extends Handler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        XapiBadRequestException::class,
        XapiAuthenticationException::class,
        XapiAuthorizationException::class,
        XapiNotFoundException::class,
        XapiConflictException::class,
        XapiPreconditionFailedException::class,
        XapiNoContentException::class,
        XapiValidationException::class,
        SimpleException::class,
    ];

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
        // xAPI exceptions.
        if ($exception instanceof XapiBadRequestException
            || $exception instanceof XapiAuthenticationException
            || $exception instanceof XapiAuthorizationException
            || $exception instanceof XapiNotFoundException
            || $exception instanceof XapiConflictException
            || $exception instanceof XapiPreconditionFailedException
            || $exception instanceof XapiNoContentException
            || $exception instanceof XapiValidationException
        ) {
            $this->logXapiError($request, $exception);
            return $this->xapiExceptionResponse($exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Return a response for the xAPI error.
     *
     * @param  \Trax\Framework\Exceptions\HttpException  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    protected function xapiExceptionResponse(HttpException $exception)
    {
        return response(
            $exception->getMessageWithErrors(),
            $exception->status(),
            $exception->headers()
        );
    }

    /**
     * Log an xAPI error.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Trax\Framework\Exceptions\HttpException  $exception
     * @return void
     */
    protected function logXapiError(Request $request, HttpException $exception): void
    {
        $method = strtoupper($request->method());
        $status = $exception->status();
        $data = [];

        // When the exception returns an error.
        // XapiNoContentException does not!
        if ($status != 200 && $status != 204) {

            // Request.
            $headers = array_map(function ($header) {
                return implode(',', $header);
            }, $request->headers->all());

            $data = [
                'headers' => $headers,
                'status' => $status,
                'phrase' => $exception->getMessage(),
            ];

            if (!empty($request->query())) {
                $data['params'] = $request->query();
            }

            if (!empty($exception->data())) {
                $data['content'] = $exception->data();
            }

            if (!empty($exception->errors())) {
                $data['errors'] = $exception->errors();
            }
        }

        // Logging.
        Logger::xapi($status, $data);
    }
}
