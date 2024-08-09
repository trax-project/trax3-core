<?php

namespace Trax\Statements\Repos\Statement\Conditions;

use Trax\Framework\Xapi\Helpers\XapiActivity;

class ActivityConditions
{
    /**
     * Get the filtering conditions for a searched IRI.
     *
     * @param  string  $iri
     * @return array
     */
    public static function hidCondition(string $iri): array
    {
        return ['object_id' => XapiActivity::hashId($iri)];
    }

    /**
     * Get the filtering conditions for a searched IRI.
     *
     * @param  string  $iri
     * @return array
     */
    public static function relatedCondition(string $iri): array
    {
        return ['activity_ids[*]' => XapiActivity::hashId($iri)];
    }
}
