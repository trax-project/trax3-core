<?php

namespace Trax\Statements\Repos\Statement\Conditions;

use Trax\Framework\Xapi\Helpers\XapiAgent;

class AgentConditions
{
    /**
     * Get the filtering conditions for a searched agent.
     *
     * @param  array  $agent
     * @return array
     */
    public static function hidCondition(array $agent): array
    {
        return ['actor_id' => XapiAgent::hashId($agent)];
    }

    /**
     * Get the filtering conditions for a searched agent.
     *
     * @param  array  $agent
     * @return array
     */
    public static function hidConditionOnObject(array $agent): array
    {
        return ['object_id' => XapiAgent::hashId($agent)];
    }

    /**
     * Get the filtering conditions for a searched agent.
     *
     * @param  array  $agent
     * @return array
     */
    public static function relatedCondition(array $agent): array
    {
        return ['agent_ids[*]' => XapiAgent::hashId($agent)];
    }

    /**
     * Get the filtering conditions for a given path and a searched agent.
     *
     * @param  string  $path
     * @param  array  $agent
     * @return array
     */
    public static function jsonCondition(string $path, array $agent): array
    {
        // Mbox.
        if (isset($agent['mbox'])) {
            return [$path.'->mbox' => $agent['mbox']];
        }
        // mbox_sha1sum.
        if (isset($agent['mbox_sha1sum'])) {
            return [$path.'->mbox_sha1sum' => $agent['mbox_sha1sum']];
        }
        // openid.
        if (isset($agent['openid'])) {
            return [$path.'->openid' => $agent['openid']];
        }
        // Account.
        if (isset($agent['account'])) {
            return ['$and' => [
                $path.'->account->name' => $agent['account']['name'],
                $path.'->account->homePage' => $agent['account']['homePage'],
            ]];
        }
    }
}
