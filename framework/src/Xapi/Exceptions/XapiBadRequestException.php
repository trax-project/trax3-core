<?php

namespace Trax\Framework\Xapi\Exceptions;

class XapiBadRequestException extends XapiException
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
    protected $message = 'xAPI Bad Request Exception.';
}
