<?php

namespace Trax\Framework\Xapi\Helpers;

use Trax\Framework\Repo\Query;
use Trax\Framework\Xapi\Helpers\XapiDate;
use Trax\Framework\Xapi\Helpers\XapiAgent;
use Trax\Framework\Xapi\Helpers\XapiActivity;

trait HasDocumentFilters
{
    /**
     * @param  string  $id
     * @param  \Trax\Framework\Repo\Query  $query
     * @return array
     */
    public function stateIdFilter($id, Query $query = null)
    {
        return [
            ['state_id' => $id],
        ];
    }

    /**
     * @param  string  $id
     * @param  \Trax\Framework\Repo\Query  $query
     * @return array
     */
    public function profileIdFilter($id, Query $query = null)
    {
        return [
            ['profile_id' => $id],
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
            ['activity_id' => XapiActivity::hashId($iri)],
        ];
    }

    /**
     * @param  string|array|object  $json
     * @param  \Trax\Framework\Repo\Query  $query
     * @return array
     */
    public function agentFilter($json, Query $query = null)
    {
        return [
            ['agent_id' => XapiAgent::hashId($json)]
        ];
    }

    /**
     * @param  array  $personHashIds
     * @param  \Trax\Framework\Repo\Query  $query
     * @return array
     */
    public function personFilter($personHashIds, Query $query = null)
    {
        return [
            ['agent_id' => ['$in' => $personHashIds]]
        ];
    }

    /**
     * @param  string  $isoDate
     * @param  \Trax\Framework\Repo\Query  $query
     * @return array
     */
    public function sinceFilter(string $isoDate, Query $query = null)
    {
        return [
            ['stored' => ['$gt' => XapiDate::normalize($isoDate)]],
        ];
    }

    /**
     * @param  string  $mimeType
     * @param  \Trax\Framework\Repo\Query  $query
     * @return array
     */
    public function typeFilter(string $mimeType, Query $query = null)
    {
        return [
            ['content_type' => $mimeType],
        ];
    }
}
