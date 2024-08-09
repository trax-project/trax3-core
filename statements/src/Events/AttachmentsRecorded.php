<?php
 
namespace Trax\Statements\Events;

use Trax\Framework\Service\Event\Event;
 
class AttachmentsRecorded extends Event
{
    /**
     * Event stream.
     *
     * @return string
     */
    public function stream(): string
    {
        return 'attachments-store';
    }
    
    /**
     * Event name.
     *
     * @return string
     */
    public function name(): string
    {
        return 'attachments-recorded';
    }
}
