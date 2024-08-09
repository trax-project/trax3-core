<?php

namespace Trax\Statements\Repos\Statement\Conditions;

use Trax\Framework\Xapi\Helpers\XapiType;

class TypeConditions
{
    /**
     * Get the filtering conditions for a given searched IRI.
     *
     * @param  string  $iri
     * @return array
     */
    public static function hidCondition(string $iri): array
    {
        return ['type_id' => XapiType::hashId($iri)];
    }
}
