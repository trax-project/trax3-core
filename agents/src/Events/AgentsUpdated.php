<?php
 
namespace Trax\Agents\Events;
 
use Trax\Framework\Service\Event\Event;
 
class AgentsUpdated extends Event
{
    /**
     * Event stream.
     *
     * @return string
     */
    public function stream(): string
    {
        return 'agents-store';
    }
    
    /**
     * Event name.
     *
     * @return string
     */
    public function name(): string
    {
        return 'agents-updated';
    }
}
