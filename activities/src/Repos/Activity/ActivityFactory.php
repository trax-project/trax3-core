<?php

namespace Trax\Activities\Repos\Activity;

use Trax\Framework\Repo\ModelFactory;
use Trax\Framework\Context;
use Trax\Framework\Xapi\Helpers\XapiActivity;
use Trax\Framework\Xapi\Helpers\XapiType;
use Trax\Framework\Xapi\Helpers\Json;
use Trax\Framework\Xapi\Helpers\StdClass;

class ActivityFactory extends ModelFactory
{
    /**
     * Return the model class.
     *
     * @return string
     */
    public function modelClass(): string
    {
        return Activity::class;
    }

    /**
     * Return the unique columns used to upsert data.
     *
     * @return array
     */
    public function uniqueColumns(): array
    {
        return ['id', 'store'];
    }

    /**
     * Prepare data before recording (used for bulk update).
     *
     * @param  array  $data
     * @return array
     */
    public function prepare(array $data)
    {
        // prepareXapiActivity must be used to insert directly new xAPI activities (e.g. tests).
        // This function is used to update activity definitions.
        // It is also used during imports, but the data comes from prepareXapiActivity.

        // Set value of the definition.
        $definition = isset($data['definition']) ? $data['definition'] : Json::object();
        $isProfile = $this->isProfile($definition);

        $now = traxIsoNow();

        // Return the record.
        return [
            'id' => isset($data['id']) ? $data['id'] : XapiActivity::hashId($data['iri']),
            'iri' => $data['iri'],
            'definition' => Json::encode($definition, $this->jsonEncode),
            'type_id' => isset($data['type_id'])
                ? $data['type_id']
                : (isset($definition->type) ? XapiType::hashId($definition->type) : null),
            'is_category' => isset($data['is_category'])
                ? $data['is_category']
                : $isProfile,
            'is_profile' => isset($data['is_profile'])
                ? $data['is_profile']
                : $isProfile,
            'store' => Context::store(),
            'stored' => isset($data['stored']) ? $data['stored'] : $now,
            'updated' => $now,
        ];
    }

    /**
     * Prepare data for export.
     *
     * @param  \Illuminate\Database\Eloquent\Model|object  $model
     * @param  bool  $rawFormat
     * @return array
     */
    public function export($model, bool $rawFormat = false): array
    {
        // ID is required by the exporter in order to create DB records.
        // Data partitioning may be helpful.
        // Stored and updated are required by the exporter in order to keep the last timestamp.
        $data = StdClass::only($model, [
            'id', 'store', 'stored', 'updated',
        ]);

        $data['activity'] = XapiActivity::jsonByModel($model);

        return $data;
    }

    /**
     * Prepare data for import (upsert).
     *
     * @param  object  $item
     * @return array
     */
    public function import(object $item): array
    {
        // The import process will use the `prepare` method which requires most of the attributes.
        return $this->prepareXapiActivity($item->activity);
    }

    /**
     * Prepare data before recording (used for bulk insert).
     * This function differs from `prepare` because it take an xAPI activity in parameter.
     *
     * @param  string|object|array  $activity
     * @return array
     */
    public function prepareXapiActivity($activity)
    {
        // Be sure to work with objects.
        $activity = Json::object($activity);
        
        // Set value of the definition.
        $definition = isset($activity->definition) ? $activity->definition : Json::object();
        $isProfile = $this->isProfile($definition);

        $now = traxIsoNow();

        // Return the record.
        return [
            'id' => XapiActivity::hashId($activity->id),
            'iri' => $activity->id,
            'definition' => Json::encode($definition, $this->jsonEncode),
            'type_id' => isset($definition->type) ? XapiType::hashId($definition->type) : null,
            'is_category' => $isProfile,
            'is_profile' => $isProfile,
            'store' => Context::store(),
            'stored' => $now,
            'updated' => $now,
        ];
    }
    
    /**
     * Update the definition of an activity.
     *
     * @param  object|array  $definition
     * @return bool
     */
    public function isProfile($definition): bool
    {
        // Be sure to work with objects.
        $definition = Json::object($definition);

        return isset($definition->type) && $definition->type == 'http://adlnet.gov/expapi/activities/profile';
    }

    /**
     * Update an existing model instance, given some data.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $data
     * @param  bool  $mergeDefinition
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($model, array $data, $mergeDefinition = false)
    {
        // Update the definition, and that's it.
        if (!isset($data['definition'])) {
            return $model;
        }

        // Merge definitions.
        if ($mergeDefinition) {
            $source = isset($model->definition) ? $model->definition : Json::object();
            $definition = Json::merge($source, $data['definition'], 2);
        } else {
            $definition = $data['definition'];
        }
    
        $model->fill([
            'definition' => $definition,
            'type_id' => isset($definition->type) ? XapiType::hashId($definition->type) : null,
            'is_profile' => $model->is_profile || $this->isProfile($definition),
            'stored' => traxIsoNow(),
        ]);

        return $model;
    }
}
