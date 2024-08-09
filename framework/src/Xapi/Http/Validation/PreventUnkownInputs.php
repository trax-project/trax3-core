<?php

namespace Trax\Framework\Xapi\Http\Validation;

use Illuminate\Http\Request;
use Trax\Framework\Xapi\Exceptions\XapiBadRequestException;

trait PreventUnkownInputs
{
    /**
     * Prevent the use of unknown inputs in the request.
     * Return the known inputs.
     *
     * @param \Illuminate\Http\Request  $request;
     * @param array  $knownInputs;
     * @return  array
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiBadRequestException
     */
    protected function preventUnkownInputs(Request $request, array $knownInputs): array
    {
        // Get inputs.
        if ($request->isJson()) {
            $inputs = $request->query();
        } else {
            $inputs = $request->all();
        }

        // Include the 'method' param.
        if (!in_array('method', $knownInputs)) {
            $knownInputs[] = 'method';
        }

        // Check them.
        foreach ($inputs as $key => $value) {
            if (!in_array($key, $knownInputs)) {
                throw new XapiBadRequestException("A request input is not allowed: [$key].");
            }
        }
        return $inputs;
    }
}
