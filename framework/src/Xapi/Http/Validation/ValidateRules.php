<?php

namespace Trax\Framework\Xapi\Http\Validation;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Trax\Framework\Xapi\Exceptions\XapiBadRequestException;

trait ValidateRules
{
    /**
     * Validate the request rules.
     *
     * @param \Illuminate\Http\Request  $request;
     * @param array  $rules;
     * @return array
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiBadRequestException
     */
    protected function validateRules(Request $request, array $rules)
    {
        try {
            return $request->validate($rules);
        } catch (ValidationException $e) {
            throw new XapiBadRequestException('One or more request inputs are not valid.', null, $e->errors());
        }
    }
}
