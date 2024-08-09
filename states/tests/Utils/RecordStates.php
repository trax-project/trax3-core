<?php

namespace Trax\States\Tests\Utils;

use Trax\Framework\Context;
use Trax\States\StatesDatabase;
use Trax\States\Repos\State\StateRepository;
use Trax\States\Recording\StateRecorder;

trait RecordStates
{
    protected function clearStates(string $store = 'default')
    {
        Context::setStore($store);

        app(StatesDatabase::class)->clear();
    }

    protected function createState(string $activityId, array $agent, string $stateId, string $registration = null, $content = '', string $contentType = 'application/json', string $store = 'default')
    {
        Context::setStore($store);

        // Remove the state.
        app(StateRepository::class)->deleteOne($activityId, $agent, $stateId, $registration);

        // Create the state.
        return app(StateRepository::class)->create([
            'activity_iri' => $activityId,
            'agent' => $agent,
            'state_id' => $stateId,
            'registration' => $registration,
            'content' => is_string($content) ? $content : json_encode($content),
            'content_type' => $contentType,
        ]);
    }

    protected function mergeState(string $activityId, array $agent, string $stateId, string $registration = null, $content = '', string $contentType = 'application/json', string $store = 'default')
    {
        Context::setStore($store);

        // Get the state.
        $resource = app(StateRepository::class)->getOne($activityId, $agent, $stateId, $registration);

        // Update and return the merged state.
        return app(StateRepository::class)->updateModel($resource, [
            'content' => is_string($content) ? $content : json_encode($content),
            'content_type' => $contentType,
        ], true);
    }

    protected function recordState(string $activityId, array $agent, string $stateId, string $registration = null, $content = '', string $contentType = 'application/json', string $store = 'default')
    {
        Context::setStore($store);

        app(StateRecorder::class)->record([[
            'activity_iri' => $activityId,
            'agent' => $agent,
            'state_id' => $stateId,
            'registration' => $registration,
            'content' => is_string($content) ? $content : json_encode($content),
            'content_type' => $contentType,
        ]]);
    }
}
