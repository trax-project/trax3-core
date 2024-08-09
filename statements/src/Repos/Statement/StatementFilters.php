<?php

namespace Trax\Statements\Repos\Statement;

use Trax\Framework\Repo\Query;
use Trax\Framework\Xapi\Helpers\XapiDate;
use Trax\Framework\Xapi\Helpers\Json;
use Trax\Statements\Repos\Statement\Conditions\AgentConditions;
use Trax\Statements\Repos\Statement\Conditions\VerbConditions;
use Trax\Statements\Repos\Statement\Conditions\ActivityConditions;
use Trax\Statements\Repos\Statement\Conditions\TypeConditions;

trait StatementFilters
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
            'statementId',
            'voidedStatementId',
            'agent',
            'verb',
            'activity',
            'since',
            'until',

            // Extended filters.
            'type',
            'profile',
            'person',
        ];
    }

    /**
     * @param  string  $id
     * @param  \Trax\Framework\Repo\Query  $query
     * @return array
     */
    public function statementIdFilter($id, Query $query = null)
    {
        return [
            ['voided' => false],
            ['id' => $id],
        ];
    }

    /**
     * @param  string  $id
     * @param  \Trax\Framework\Repo\Query  $query
     * @return array
     */
    public function voidedStatementIdFilter($id, Query $query = null)
    {
        return [
            ['voided' => true],
            ['id' => $id],
        ];
    }

    /**
     * @param  string|array|object  $agent
     * @param  \Trax\Framework\Repo\Query  $query
     * @return array
     */
    public function agentFilter($agent, Query $query = null)
    {
        // Be sure to work with an array.
        $agent = Json::array($agent);

        // Extended: agent_location.
        if ($query->option('agent_location') == 'actor') {
            return [
                AgentConditions::hidCondition($agent),
            ];
        }
        if ($query->option('agent_location') == 'object') {
            return [
                AgentConditions::hidConditionOnObject($agent),
            ];
        }
        if ($query->option('agent_location') == 'everywhere') {
            return [
                AgentConditions::relatedCondition($agent)
            ];
        }
    
        // Simple.
        if (is_null($query) || !$query->hasOption('related_agents') || $query->option('related_agents') == 'false') {
            // Keep the condition in an array.
            return [
                ['$or' => [
                    AgentConditions::hidCondition($agent),
                    AgentConditions::hidConditionOnObject($agent),

                    // Not clear if we should look into the members.
                    // Seems to be the case in the spec, not in the test suite.
                    //AgentConditions::jsonCondition('raw->actor->member[*]', $agent),
                    //AgentConditions::jsonCondition('raw->object->member[*]', $agent),
                ]]
            ];
        }

        // Related.
        return [
            AgentConditions::relatedCondition($agent)
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
            ['$or' => [
                ['actor_id' => ['$in' => $personHashIds]],
                ['object_id' => ['$in' => $personHashIds]],
            ]]
        ];
    }

    /**
     * @param  string  $iri
     * @param  \Trax\Framework\Repo\Query  $query
     * @return array
     */
    public function verbFilter($iri, Query $query = null)
    {
        return [
            VerbConditions::hidCondition($iri),
        ];
    }

    /**
     * @param  string  $iri
     * @param  \Trax\Framework\Repo\Query  $query
     * @return array
     */
    public function activityFilter($iri, Query $query = null)
    {
        // Extended: activity_location.
        if ($query->option('activity_location') == 'object') {
            return [
                ActivityConditions::hidCondition($iri),
            ];
        }
        if ($query->option('activity_location') == 'everywhere') {
            return [
                ActivityConditions::relatedCondition($iri)
            ];
        }

        // Simple.
        if (is_null($query) || !$query->hasOption('related_activities') || $query->option('related_activities') == 'false') {
            return [
                ActivityConditions::hidCondition($iri),
            ];
        }

        // Related.
        return [
            ActivityConditions::relatedCondition($iri)
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
     * @param  string  $isoDate
     * @param  \Trax\Framework\Repo\Query  $query
     * @return array
     */
    public function untilFilter(string $isoDate, Query $query = null)
    {
        return [
            ['stored' => ['$lte' => XapiDate::normalize($isoDate)]],
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
            TypeConditions::hidCondition($iri),
        ];
    }

    /**
     * @param  string  $iri
     * @param  \Trax\Framework\Repo\Query  $query
     * @return array
     */
    public function profileFilter($iri, Query $query = null)
    {
        return [
            ActivityConditions::relatedCondition($iri)
        ];
    }
}
