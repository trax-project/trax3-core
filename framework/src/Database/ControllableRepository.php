<?php

namespace Trax\Framework\Database;

use Illuminate\Support\Collection;

abstract class ControllableRepository
{
    /**
     * Get the resources.
     *
     * @return \Illuminate\Support\Collection
     */
    abstract public function get(): Collection;

    /**
     * Check if we can communicate with the database.
     *
     * @return object  (object)['ready' => false, 'reason' => 'Authentication failed']
     */
    public function checkStatus()
    {
        try {
            $this->get();
            return (object)['ready' => true, 'reason' => ''];
        } catch (\Exception $e) {
            return (object)['ready' => false, 'reason' => $e->getMessage()];
        }
    }
}
