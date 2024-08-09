<?php

namespace Trax\Framework\Logging;

use Illuminate\Support\Facades\Facade;

class Logger extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Trax\Framework\Logging\LoggerContract::class;
    }
}
