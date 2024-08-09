<?php

namespace Trax\Framework\Xapi\Http\Controllers;

use Illuminate\Http\Request;
use Trax\Framework\Logging\Logger;
use Trax\Framework\Http\Controllers\Controller;

class AboutController extends Controller
{
    /**
     * Get a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiBadRequestException
     */
    public function get(Request $request)
    {
        // Logging.
        Logger::xapi(200);

        return response()->json(
            config('trax.xapi.about')
        );
    }
}
