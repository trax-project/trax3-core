<?php

namespace Trax\Framework\Service\Event\Redis;

use Illuminate\Support\Facades\Redis as RedisFacade;
use Redis;

class RedisDatabase
{
    /**
     * Get a status.
     *
     * @return object
     */
    public static function status(): object
    {
        if (!class_exists(Redis::class)) {
            return (object)['ready' => false, 'reason' => 'Redis PHP extension is not installed!'];
        }

        try {
            // Just make a get to throw an exception if Redis is not ready.
            RedisFacade::get('redis-status');
        } catch (\Exception $e) {
            return (object)['ready' => false, 'reason' => $e->getMessage()];
        }

        return (object)['ready' => true, 'reason' => ''];
    }
}
