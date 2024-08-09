<?php

namespace Trax\Framework\Xapi\Exceptions;

class XapiPreconditionFailedException extends XapiException
{
    /**
     * Status code that should be inserted in the HTTP response.
     *
     * @var int
     */
    protected $status = 412;

    /**
     * Default validation message.
     *
     * @var string
     */
    protected $message = 'xAPI Precondition Failed.';
}
