<?php

namespace Trax\Agents\Repos\Agent;

use Trax\Framework\Repo\Query;
use Trax\Framework\Xapi\Helpers\XapiAgent;

trait AgentFilters
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
            'agent',

            // Extended filters.
            'type',
            'person',
        ];
    }

    /**
     * @param  string|array|object  $json
     * @param  \Trax\Framework\Repo\Query  $query
     * @return array
     */
    public function agentFilter($json, Query $query = null)
    {
        if ($query->option('location') == 'agent') {
            return [
                ['id' => XapiAgent::hashId($json)],
                ['is_group' => false],
            ];
        }
        if ($query->option('location') == 'group') {
            return [
                ['id' => XapiAgent::hashId($json)],
                ['is_group' => true],
            ];
        }
        if ($query->option('location') == 'member') {
            return [
                ['members[*]' => XapiAgent::hashId($json)],
            ];
        }
        if ($query->option('location') == 'all') {
            return [
                ['$or' => [
                    ['id' => XapiAgent::hashId($json)],
                    ['members[*]' => XapiAgent::hashId($json)],
                ]]
            ];
        }
        return [
            ['id' => XapiAgent::hashId($json)],
        ];
    }

    /**
     * @param  string  $type
     * @param  \Trax\Framework\Repo\Query  $query
     * @return array
     */
    public function typeFilter($type, Query $query = null)
    {
        if ($type == 'all') {
            return [];
        }
        if ($type == 'agents') {
            return [['is_group' => false]];
        }
        if ($type == 'groups') {
            return [['is_group' => true]];
        }
        if ($type == 'groups_with_members') {
            return [['members_count' => ['$gt' => 0]]];
        }
    }

    /**
     * @param  array  $personHashIds
     * @param  \Trax\Framework\Repo\Query  $query
     * @return array
     */
    public function personFilter($personHashIds, Query $query = null)
    {
        return [
            ['id' => ['$in' => $personHashIds]]
        ];
    }
}
