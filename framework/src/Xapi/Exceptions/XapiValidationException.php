<?php

namespace Trax\Framework\Xapi\Exceptions;

use Trax\Framework\Exceptions\HttpException;

class XapiValidationException extends HttpException
{
    /**
     * Status code that should be inserted in the HTTP response.
     *
     * @var int
     */
    protected $status = 400;

    /**
     * Default message.
     *
     * @var string
     */
    protected $message = 'xAPI Validation Error(s).';
}
