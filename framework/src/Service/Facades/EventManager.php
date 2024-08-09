<?php

namespace Trax\Framework\Service\Facades;

use Illuminate\Support\Facades\Facade;

class EventManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Trax\Framework\Service\Event\EventManager::class;
    }
}
