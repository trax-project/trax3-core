<?php
 
namespace Trax\Statements\Events;
 
use Trax\Framework\Service\Event\Event;
 
class StatementsRecorded extends Event
{
    /**
     * Event stream.
     *
     * @return string
     */
    public function stream(): string
    {
        return 'statements-store';
    }
    
    /**
     * Event name.
     *
     * @return string
     */
    public function name(): string
    {
        return 'statements-recorded';
    }
}
