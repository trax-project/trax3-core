<?php

namespace Trax\AgentProfiles\Tests\Utils;

use Trax\Framework\Context;
use Trax\AgentProfiles\Repos\AgentProfile\AgentProfileRepository;
use Trax\AgentProfiles\Recording\AgentProfileRecorder;

trait RecordAgentProfiles
{
    protected function clearAgentProfiles(string $store = 'default')
    {
        Context::setStore($store);
        
        app(AgentProfileRepository::class)->clear();
    }

    protected function createAgentProfile(array $agent, string $profileId, $content, string $contentType = 'application/json', string $store = 'default')
    {
        Context::setStore($store);

        // Remove the profile.
        app(AgentProfileRepository::class)->deleteOne($agent, $profileId);

        // Create the profile.
        return app(AgentProfileRepository::class)->create([
            'agent' => $agent,
            'profile_id' => $profileId,
            'content' => is_string($content) ? $content : json_encode($content),
            'content_type' => $contentType,
        ]);
    }

    protected function mergeAgentProfile(array $agent, string $profileId, $content, string $contentType = 'application/json', string $store = 'default')
    {
        Context::setStore($store);

        // Get the profile.
        $resource = app(AgentProfileRepository::class)->getOne($agent, $profileId);

        // Update and return the merged profile.
        return app(AgentProfileRepository::class)->updateModel($resource, [
            'content' => is_string($content) ? $content : json_encode($content),
            'content_type' => $contentType,
        ], true);
    }

    protected function recordAgentProfile(array $agent, string $profileId, $content, string $contentType = 'application/json', string $store = 'default')
    {
        Context::setStore($store);

        app(AgentProfileRecorder::class)->record([[
            'agent' => $agent,
            'profile_id' => $profileId,
            'content' => is_string($content) ? $content : json_encode($content),
            'content_type' => $contentType,
        ]]);
    }
}
