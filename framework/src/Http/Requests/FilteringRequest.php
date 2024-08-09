<?php

namespace Trax\Framework\Http\Requests;

use Illuminate\Http\Request;
use Trax\Framework\Repo\CrudRequest;
use Trax\Framework\Http\Validation\ValidateFilteringRequest;

class FilteringRequest
{
    use ValidateFilteringRequest;

    /**
     * Filters rules.
     */
    protected $filtersRules = [];
    
    /**
     * Options rules.
     */
    protected $optionsRules = [];

    /**
     * Validate the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Trax\Framework\Repo\CrudRequest
     */
    public function validate(Request $request): CrudRequest
    {
        return $this->validateFilteringRequest($request);
    }
}
