<?php

namespace Trax\Activities\Recording;

use Illuminate\Support\Collection;
use Trax\Framework\Context;
use Trax\Framework\Service\Facades\EventManager;
use Trax\Framework\Xapi\Helpers\Json;
use Trax\Framework\Xapi\Helpers\XapiActivity;
use Trax\Activities\ActivitiesService;
use Trax\Activities\Repos\Activity\ActivityRepository;
use Trax\Activities\Events\ActivitiesUpdated;

class ActivityUpdater
{
    /**
     * @var \Trax\Activities\Repos\Activity\ActivityRepository
     */
    protected $activities;
    
    /**
     * @var \Trax\Framework\Service\MicroService
     */
    protected $service;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->activities = app(ActivityRepository::class);
        $this->service = app(ActivitiesService::class);
    }

    /**
     * Update the activities of a collection of statements.
     * Each statement takes the form of an array of properties as it has been inserted into the database.
     *
     * @param   \Illuminate\Support\Collection|array  $statements
     * @return void
     */
    public function update($statements): void
    {
        $pipeline = Context::pipeline();
        
        if (!$pipeline->update_activities) {
            return;
        }

        // Update activities.
        $records = $this->updateStatementsActivities($statements);

        // Dispatch events.
        if (!$records->isEmpty()) {
            EventManager::dispatch(ActivitiesUpdated::class, $records);
        }
    }

    /**
     * Update the activities of a collection of statements.
     * Each statement takes the form of an array of properties as it has been inserted into the database.
     *
     * @param   \Illuminate\Support\Collection|array  $statements
     * @return \Illuminate\Support\Collection
     */
    protected function updateStatementsActivities($statements): Collection
    {
        // Be sure to work with a collection.
        if (is_array($statements)) {
            $statements = collect($statements);
        }

        // Extract activities and keep the last definitions from the batch.
        $incoming = $this->activitiesInfo($statements);

        // Exit if nothing to update.
        if ($incoming->isEmpty()) {
            return $incoming;
        }

        // Update the activity definitions.
        $incoming = $this->updateActivityDefinitions($incoming);

        // Update the activity table.
        return $this->insertOrUpdateActivities($incoming);
    }

    /**
     * Extract activities from a collection of statements.
     *
     * @param   \Illuminate\Support\Collection  $statements
     * @return \Illuminate\Support\Collection
     */
    protected function activitiesInfo(Collection $statements): Collection
    {
        // We collect all the activities, and we group them by their id.
        return collect($statements->reduce(function ($info, $statement) {
            return $info->merge(
                collect(XapiActivity::references(is_array($statement) ? $statement['raw'] : $statement->raw, false))
            );
        }, collect()))->groupBy('id')

        // For each group, we merge the definition.
        ->map(function ($items, $id) {
            $merged = $items->last();
            $merged['definition'] = $items->reduce(function ($mergedDef, $item) {
                return isset($item['definition']) ? Json::merge($mergedDef, $item['definition'], 2) : $mergedDef;
            }, Json::object());
            return $merged;
        });
    }

    /**
     * Update activity definitions.
     *
     * @param  \Illuminate\Support\Collection  $incoming
     * @return \Illuminate\Support\Collection
     */
    protected function updateActivityDefinitions(Collection $incoming): Collection
    {
        // Get the existing activities.
        $existing = $this->activities->in($incoming->pluck('id'));

        // Update incoming infos.
        return $incoming->map(function ($info) use ($existing) {
            if ($model = $existing->where('id', $info['id'])->first()) {
                // Here we should check if there is a difference with the existing definition.
                // If there is no difference, we should return null, and then filter the incoming collection.
                $info['definition'] = Json::merge($model->definition, $info['definition'], 2);
                $info['is_category'] = $model->is_category || $info['location'] == 'category';
                $info['is_profile'] = $model->is_profile;
                $info['stored'] = $model->stored;
            } else {
                $info['is_category'] = $info['location'] == 'category';
                // We don't determine here if it is a profile or not.
                $info['is_profile'] = false;
            }
            return $info;
        });
    }

    /**
     * Insert or update activities.
     *
     * @param  \Illuminate\Support\Collection  $incoming
     * @return \Illuminate\Support\Collection
     */
    protected function insertOrUpdateActivities(Collection $incoming): Collection
    {
        $activities = $incoming->map(function ($info) {
            $record = [
                'id' => $info['id'],
                'iri' => $info['iri'],
                'definition' => $info['definition'],
                'is_category' => $info['is_category'],
            ];
            if ($info['is_profile']) {
                // Don't add it to the record if it is not known as a profile
                // in order to let the factory determine if it is a profile.
                $record['is_profile'] = $info['is_profile'];
            }
            if (isset($info['stored'])) {
                $record['stored'] = $info['stored'];
            }
            return $record;
        })->toArray();

        return collect(
            $this->activities->upsert($activities)
        );
    }
}
