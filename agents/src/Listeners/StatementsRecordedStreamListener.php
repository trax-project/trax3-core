<?php
 
namespace Trax\Agents\Listeners;

use Trax\Framework\Service\Event\StreamListener;

class StatementsRecordedStreamListener extends StreamListener
{
    /**
     * The service.
     *
     * @var string
     */
    protected $service = 'agents';

    /**
     * The events to dispatch locally.
     * An empty list means that all the events are dispatched.
     *
     * @var array
     */
    protected $accept = ['statements-recorded'];
}
