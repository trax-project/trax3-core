<?php

namespace Trax\Framework\Xapi\Schema;

use Trax\Framework\Xapi\Schema\Contracts\Validator;
use Trax\Framework\Xapi\Schema\Traits\ValidateStatements;
use Trax\Framework\Xapi\Schema\Traits\CompareStatements;
use Trax\Framework\Xapi\Schema\Traits\FormatStatements;

class Statement implements Validator
{
    use ValidateStatements, CompareStatements, FormatStatements;
}
