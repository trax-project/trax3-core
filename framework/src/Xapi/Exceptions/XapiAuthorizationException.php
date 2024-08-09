<?php

namespace Trax\Framework\Xapi\Exceptions;

class XapiAuthorizationException extends XapiException
{
    /**
     * Status code that should be inserted in the HTTP response.
     *
     * @var int
     */
    protected $status = 403;

    /**
     * Default validation message.
     *
     * @var string
     */
    protected $message = 'xAPI Authorization Exception.';
}
