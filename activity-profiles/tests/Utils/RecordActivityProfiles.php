<?php

namespace Trax\ActivityProfiles\Tests\Utils;

use Trax\Framework\Context;
use Trax\ActivityProfiles\Repos\ActivityProfile\ActivityProfileRepository;
use Trax\ActivityProfiles\Recording\ActivityProfileRecorder;

trait RecordActivityProfiles
{
    protected function clearActivityProfiles(string $store = 'default')
    {
        Context::setStore($store);

        app(ActivityProfileRepository::class)->clear();
    }

    protected function createActivityProfile(string $activityId, string $profileId, $content, string $contentType = 'application/json', string $store = 'default')
    {
        Context::setStore($store);

        // Remove the profile.
        app(ActivityProfileRepository::class)->deleteOne($activityId, $profileId);

        // Create the profile.
        return app(ActivityProfileRepository::class)->create([
            'activity_iri' => $activityId,
            'profile_id' => $profileId,
            'content' => is_string($content) ? $content : json_encode($content),
            'content_type' => $contentType,
        ]);
    }

    protected function mergeActivityProfile(string $activityId, string $profileId, $content, string $contentType = 'application/json', string $store = 'default')
    {
        Context::setStore($store);

        // Get the profile.
        $resource = app(ActivityProfileRepository::class)->getOne($activityId, $profileId);

        // Update and return the merged profile.
        return app(ActivityProfileRepository::class)->updateModel($resource, [
            'content' => is_string($content) ? $content : json_encode($content),
            'content_type' => $contentType,
        ], true);
    }

    protected function recordActivityProfile(string $activityId, string $profileId, $content, string $contentType = 'application/json', string $store = 'default')
    {
        Context::setStore($store);

        app(ActivityProfileRecorder::class)->record([[
            'activity_iri' => $activityId,
            'profile_id' => $profileId,
            'content' => is_string($content) ? $content : json_encode($content),
            'content_type' => $contentType,
        ]]);
    }
}
