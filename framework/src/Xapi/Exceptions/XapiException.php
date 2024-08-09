<?php

namespace Trax\Framework\Xapi\Exceptions;

use Trax\Framework\Exceptions\HttpException;

abstract class XapiException extends HttpException
{
    /**
     * Headers that should be inserted in the HTTP response.
     *
     * @var array
     */
    protected $headers = ['X-Experience-API-Version' => '1.0.3'];

    /**
     * Default message.
     *
     * @var string
     */
    protected $message = 'xAPI Exception.';
}
