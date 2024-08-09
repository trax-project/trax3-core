<?php

namespace Trax\Framework\Exceptions;

use Exception;

class HttpException extends Exception
{
    /**
     * Status code that should be inserted in the HTTP response.
     *
     * @var int
     */
    protected $status = 400;

    /**
     * Headers that should be inserted in the HTTP response.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Default message.
     *
     * @var string
     */
    protected $message = 'HTTP Exception.';

    /**
     * Data associated with the exception.
     *
     * @var object|array|null
     */
    protected $data = null;

    /**
     * Errors associated with the exception.
     * The format is ['prop1' => ['message1', 'message2']]
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Constructor.
     *
     * @param  string  $message
     * @param  object|array  $data
     * @param  array  $errors
     * @return void
     */
    public function __construct($message = '', $data = null, array $errors = [])
    {
        $message = empty($message) ? $this->message : $message;
        $this->data = $data;
        $this->errors = $errors;
        parent::__construct($message);
    }

    /**
     * Get the status.
     *
     * @return int
     */
    public function status(): int
    {
        return $this->status;
    }

    /**
     * Get the headers.
     *
     * @return array
     */
    public function headers(): array
    {
        return $this->headers;
    }
    
    /**
     * Get the errors.
     *
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get the data.
     *
     * @return object|array|null
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Get the message with errors.
     *
     * @return string
     */
    public function getMessageWithErrors(): string
    {
        // The format is ['prop1' => ['message1', 'message2']]
        $errors = empty($this->errors) ? '' : "\n" . json_encode($this->errors);
        return parent::getMessage() . $errors;
    }

    /**
     * Add headers to the HTTP response.
     *
     * @param  array  $headers
     * @return \Trax\Framework\Exceptions\HttpException
     */
    public function addHeaders(array $headers): HttpException
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }
}
