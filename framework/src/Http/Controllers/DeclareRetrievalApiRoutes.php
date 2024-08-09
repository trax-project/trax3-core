<?php

namespace Trax\Framework\Http\Controllers;

use Trax\Framework\Http\ProvideRoutes;

trait DeclareRetrievalApiRoutes
{
    use ProvideRoutes;

    // TO BE DEFINED
    /*
    protected static $api = [
        'key' => 'activities',
        'domain' => 'activities',
        'request' => \Trax\Activities\Http\Requests\ActivityApiRequest::class,
    ];
    */
    
    /**
     * @return array
     */
    protected static function buildRoutes(): array
    {
        return ['stores/{xstore}/' . self::$api['key'] => ['get' => 'get']];
    }
}
