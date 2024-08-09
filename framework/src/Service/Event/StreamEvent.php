<?php
 
namespace Trax\Framework\Service\Event;
 
abstract class StreamEvent
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $stream;

    /**
     * @var string
     */
    public $class;

    /**
     * @var array
     */
    public $data;

    /**
     * @var array
     */
    public $context;
}
