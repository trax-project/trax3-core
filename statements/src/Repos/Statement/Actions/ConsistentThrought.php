<?php

namespace Trax\Statements\Repos\Statement\Actions;

use Illuminate\Support\Facades\Redis;

trait ConsistentThrought
{
    /**
     * Update the consistent through value.
     *
     * @return void
     */
    public function updateConsistentThrough(): void
    {
        // We store the last recording date in a Redis field.
        // Redis::set('xapi.consistent-through', traxIsoNow());
        // Redis is not necessarily installed and used. 
    }

    /**
     * Get the consistent through value.
     *
     * @return string
     */
    public function consistentThrough(): string
    {
        // Get the last known recording date.
        // $lastStored1 = Redis::get('xapi.consistent-through');
        // Redis is not necessarily installed and used. 

        // Get the current time and apply a security margin of 5 second.
        return (new \DateTime(traxIsoNow()))
            ->sub(new \DateInterval('PT3S'))
            ->format('c');

        // Return the more recent one.
        // return empty($lastStored1) ? $lastStored2 : max($lastStored1, $lastStored2);
    }
}
