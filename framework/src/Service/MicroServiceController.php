<?php

namespace Trax\Framework\Service;

use Illuminate\Http\Request;
use Trax\Framework\Http\Controllers\Controller;
use Trax\Framework\Http\ProvideRoutes;

class MicroServiceController extends Controller
{
    use ProvideRoutes;

    /**
     * @var array
     */
    protected static $routes = [
        'check' => ['get' => 'check'],
    ];

    /**
     * Check the services.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function check(Request $request)
    {
        return response("OK \n", 200);
    }
}
