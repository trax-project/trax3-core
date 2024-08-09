<?php

namespace Trax\Activities\Repos\Activity;

use Trax\Framework\Repo\Query;
use Trax\Framework\Xapi\Helpers\XapiActivity;
use Trax\Framework\Xapi\Helpers\XapiType;

trait ActivityFilters
{
    /**
     * Get the dynamic filters.
     *
     * @return array
     */
    public function dynamicFilters(): array
    {
        return [
            // Standard filters.
            'activityId',

            // Extended filters.
            'type',
        ];
    }

    /**
     * @param  string  $iri
     * @param  \Trax\Framework\Repo\Query  $query
     * @return array
     */
    public function activityIdFilter($iri, Query $query = null)
    {
        return [
            ['id' => XapiActivity::hashId($iri)],
        ];
    }

    /**
     * @param  string  $iri
     * @param  \Trax\Framework\Repo\Query  $query
     * @return array
     */
    public function typeFilter($iri, Query $query = null)
    {
        return [
            ['type_id' => XapiType::hashId($iri)],
        ];
    }
}
