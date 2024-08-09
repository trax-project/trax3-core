<?php

namespace Trax\Activities\Tests\Utils;

use Trax\Framework\Context;
use Trax\Activities\Repos\Activity\ActivityRepository;
use Trax\Activities\Recording\ActivityUpdater;

trait RecordActivities
{
    protected function clearActivities(string $store = 'default')
    {
        Context::setStore($store);

        app(ActivityRepository::class)->clear();
    }

    protected function createActivity(array $activity, string $store = 'default')
    {
        Context::setStore($store);

        // Remove the activity.
        app(ActivityRepository::class)->deleteOne($activity['id']);

        // Create the activity.
        app(ActivityRepository::class)->insert([$activity], 'prepareXapiActivity');

        // Return the activity.
        return app(ActivityRepository::class)->getOne($activity['id']);
    }

    protected function updateActivity(array $values, string $store = 'default')
    {
        Context::setStore($store);

        // Create the first activity.
        $activity = $this->createActivity(array_shift($values), $store);

        // Update the definition with the following activities.
        foreach ($values as $value) {
            if (isset($value['definition'])) {
                app(ActivityRepository::class)->updateModel($activity, $value, true);
            }
        }

        // Return the activity.
        return app(ActivityRepository::class)->getOne($activity->iri);
    }

    protected function recordStatementsMock(array $statements, string $store = 'default')
    {
        Context::setStore($store);

        collect($statements)->each(function ($statement) {
            app(ActivityUpdater::class)->update(
                collect([['raw' => json_encode($statement)]])
            );
        });
    }

    protected function recordStatementsBatchMock(array $statements, string $store = 'default')
    {
        Context::setStore($store);

        $batch = collect($statements)->map(function ($statement) {
            return ['raw' => json_encode($statement)];
        });

        app(ActivityUpdater::class)->update($batch);
    }
}
