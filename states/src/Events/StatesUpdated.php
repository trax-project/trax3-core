<?php
 
namespace Trax\States\Events;
 
use Trax\Framework\Service\Event\Event;
 
class StatesUpdated extends Event
{
    /**
     * Event stream.
     *
     * @return string
     */
    public function stream(): string
    {
        return 'states-store';
    }
    
    /**
     * Event name.
     *
     * @return string
     */
    public function name(): string
    {
        return 'states-updated';
    }
}
