<?php

namespace Trax\Framework\Http\Validation;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory;
use Trax\Framework\Repo\CrudRequest;
use Trax\Framework\Exceptions\SimpleException;

trait ValidateFilteringRequest
{
    /**
     * Filters rules.   TO BE DEFINED
     */
    // protected $filtersRules = [];
    
    /**
     * Options rules.   TO BE DEFINED
     */
    // protected $optionsRules = [];

    /**
     * Validate the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Trax\Framework\Repo\CrudRequest
     *
     * @throws \Trax\Framework\Exceptions\SimpleException
     */
    public function validateFilteringRequest(Request $request): CrudRequest
    {
        // Validate the CRUD request.
        $params = $request->validate(
            CrudRequest::validationRules()
        );

        // Validate the filters.
        if (isset($params['filters']) && isset($this->filtersRules)) {
            $validatedFilters = app(Factory::class)->make($params['filters'], $this->filtersRules)->validate();
            if (count($params['filters']) > count($validatedFilters)) {
                $unauthorized = array_diff_key($params['filters'], $validatedFilters);
                throw new SimpleException('Trying to request data with a unauthorized filter(s): '
                    . json_encode($unauthorized));
            }
            $params['filters'] = $this->castInputs($validatedFilters, $this->filtersRules);
        }

        // Validate the options.
        if (isset($params['options']) && isset($this->optionsRules)) {
            $validatedOptions = app(Factory::class)->make($params['options'], $this->optionsRules)->validate();
            if (count($params['options']) > count($validatedOptions)) {
                $unauthorized = array_diff_key($params['options'], $validatedOptions);
                throw new SimpleException('Trying to request data with a unauthorized option(s): '
                    . json_encode($unauthorized));
            }
            $params['options'] = $this->castInputs($validatedOptions, $this->optionsRules);
        }

        // Return the CRUD request.
        return new CrudRequest($params);
    }

    /**
     * Validate the request.
     *
     * @param  array  $inputs
     * @param  array  $rules
     * @return array
     */
    protected function castInputs(array $inputs, array $rules): array
    {
        foreach ($inputs as $key => $value) {
            // No rule.
            if (!isset($rules[$key])) {
                continue;
            }

            // Boolean cast.
            if (str_contains($rules[$key], 'boolean')) {
                $inputs[$key] = $value === 1 || $value === '1' || $value === true || $value === 'true';
            }
        }
        return $inputs;
    }
}
