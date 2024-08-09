<?php
 
namespace Trax\Statements\Events;
 
use Trax\Framework\Service\Event\Event;
 
class StatementsVoided extends Event
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
        return 'statements-voided';
    }
}
