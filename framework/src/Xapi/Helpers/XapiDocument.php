<?php

namespace Trax\Framework\Xapi\Helpers;

class XapiDocument
{
    /**
     * Generate hash ID for the given state.
     *
     * @param  object|array|string  $agent
     * @param  string  $activityId
     * @param  string  $stateId
     * @param  string  $registration
     * @return string|null
     */
    public static function stateHashId($agent, string $activityId, string $stateId, string $registration = '')
    {
        if (!$agentSid = XapiAgent::stringId($agent)) {
            return null;
        }
        return HashId::create("$agentSid|$activityId|$stateId|$registration");
    }

    /**
     * Generate hash ID for the given agent profile.
     *
     * @param  object|array|string  $agent
     * @param  string  $profileId
     * @return string|null
     */
    public static function agentProfileHashId($agent, string $profileId)
    {
        if (!$agentSid = XapiAgent::stringId($agent)) {
            return null;
        }
        return HashId::create("$agentSid|$profileId");
    }

    /**
     * Generate hash ID for the given activity profile.
     *
     * @param  string  $activityId
     * @param  string  $profileId
     * @return string
     */
    public static function activityProfileHashId(string $activityId, string $profileId): string
    {
        return HashId::create("$activityId|$profileId");
    }

    /**
     * Generate hash ID for the given state or profile ID.
     *
     * @param  string  $documentId
     * @return string
     */
    public static function idHashId(string $documentId): string
    {
        return HashId::create($documentId);
    }
}
