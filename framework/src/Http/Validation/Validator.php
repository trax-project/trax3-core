<?php

namespace Trax\Framework\Http\Validation;

class Validator
{
    /**
     * Validate a data against the given rules.
     *
     * @return void
     */
    public static function check($data, $rules)
    {
        return app('validator')->make(['data' => $data], ['data' => $rules])->passes();
    }
}
