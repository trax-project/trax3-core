<?php

namespace Trax\Agents\Http\Requests;

use Trax\Framework\Http\Requests\FilteringRequest;

class AgentApiRequest extends FilteringRequest
{
    /**
     * Filters rules.
     */
    protected $filtersRules = [
        'agent' => 'xapi_agent',
        'type' => 'string|in:all,agents,groups,groups_with_members',
        'sid_type' => 'string|in:mbox,mbox_sha1sum,openid,account',
    ];
    
    /**
     * Options rules.
     */
    protected $optionsRules = [
        'location' => 'string|in:agent,group,member,all',
        'join_members' => 'json_boolean',
    ];
}
