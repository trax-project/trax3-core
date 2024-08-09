<?php

namespace Trax\Framework\Http\Controllers;

use Illuminate\Http\Request;
use Trax\Framework\Context;

trait ImplementCompletionApiMethods
{
    use ImplementRetrievalApiMethods {
        get as retrievalGet;
    }

    /**
     * @var string
     */
    //protected $pipelineProp = 'update_activity_ids';    // MUST BE DEFINED

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiBadRequestException
     */
    public function get(Request $request)
    {
        // Return an empty return if the option is disabled in the pipeline.
        if (!Context::pipeline()->{$this->pipelineProp}) {
            return response()->json([
                'data' => []
            ]);
        }

        return $this->retrievalGet($request);
    }
}
