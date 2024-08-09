<?php

namespace Trax\Framework\Xapi\Exceptions;

class XapiAuthenticationException extends XapiException
{
    /**
     * Status code that should be inserted in the HTTP response.
     *
     * @var int
     */
    protected $status = 401;

    /**
     * Default validation message.
     *
     * @var string
     */
    protected $message = 'xAPI Authentication Exception.';
}
