<?php

namespace Trax\Framework\Xapi\Schema;

use Trax\Framework\Xapi\Schema\Contracts\Validator;
use Trax\Framework\Xapi\Schema\Traits\IsValid;
use Trax\Framework\Xapi\Schema\Parsing\StatementSchema;
use Trax\Framework\Xapi\Schema\Parsing\Parser;
use Trax\Framework\Xapi\Exceptions\XapiValidationException;

class Agent implements Validator
{
    use IsValid;
    
    /**
     * Validate an agent and return a list of errors.
     *
     * @param  mixed  $data
     * @return array
     *
     * @throws \Trax\Framework\Xapi\Exceptions\XapiValidationException
     */
    public static function validate($data)
    {
        $schema = new StatementSchema();
        $parser = new Parser($schema);
        $errors = $parser->validate($data, 'agent');
        if (!empty($errors)) {
            throw new XapiValidationException('This agent is not valid.', $data, $errors);
        }
    }
}
