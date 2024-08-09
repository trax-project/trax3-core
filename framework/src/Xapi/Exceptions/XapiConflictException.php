<?php

namespace Trax\Framework\Xapi\Exceptions;

class XapiConflictException extends XapiException
{
    /**
     * Status code that should be inserted in the HTTP response.
     *
     * @var int
     */
    protected $status = 409;

    /**
     * Default message.
     *
     * @var string
     */
    protected $message = 'xAPI Conflict.';
}
