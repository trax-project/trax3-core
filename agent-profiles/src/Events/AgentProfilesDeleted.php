<?php
 
namespace Trax\AgentProfiles\Events;
 
use Trax\Framework\Service\Event\Event;
 
class AgentProfilesDeleted extends Event
{
    /**
     * Event stream.
     *
     * @return string
     */
    public function stream(): string
    {
        return 'agent-profiles-store';
    }
    
    /**
     * Event name.
     *
     * @return string
     */
    public function name(): string
    {
        return 'agent-profiles-deleted';
    }
}
