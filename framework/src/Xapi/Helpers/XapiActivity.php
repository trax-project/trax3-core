<?php

namespace Trax\Framework\Xapi\Helpers;

class XapiActivity
{
    /**
     * Generate hash for the given activity.
     *
     * @param  string  $activity
     * @return string
     */
    public static function hashId(string $activityId)
    {
        return HashId::create(
            self::stringId($activityId)
        );
    }

    /**
     * Generate a stringified ID for a given activity.
     *
     * @param  string  $activityId
     * @return string|null
     */
    public static function stringId(string $activityId)
    {
        return 'iri::' . $activityId;
    }

    /**
     * Get the activity object or array from a given activity model.
     *
     * @param  object|\Trax\Activities\Repos\Activity\Activity  $model
     * @param  bool  $asObject
     * @return array|object|null
     */
    public static function jsonByModel($model, bool $asObject = false)
    {
        $activity = [
            'objectType' => 'Activity',
            'id' => $model->iri,
        ];

        if (!empty($model->definition)) {
            $activity['definition'] = $model->definition;
        }

        return $asObject ? json_decode(json_encode($activity)) : $activity;
    }

    /**
     * Extract all the activity HIDs of the statement.
     *
     * @param  object|array|string  $statement
     * @param  bool  $sub
     * @return array
     */
    public static function referenceHids($statement): array
    {
        return array_values(array_unique(array_map(function ($reference) {
            return $reference['id'];
        }, self::references($statement))));
    }

    /**
     * Extract all the activities of the statement.
     *
     * @param  object|array|string  $statement
     * @param  bool  $short
     * @param  bool  $sub
     * @return array
     */
    public static function references($statement, bool $short = true, bool $sub = false): array
    {
        // Statement as an object.
        $statement = Json::object($statement);

        $references = [];

        if (!isset($statement->object->objectType) || $statement->object->objectType == 'Activity') {
            $references[] = self::reference($statement->object, 'object', $short, $sub);
        }

        if (isset($statement->context)) {
            $references = array_merge($references, self::contextReferences($statement->context, 'parent', $short, $sub));
            $references = array_merge($references, self::contextReferences($statement->context, 'grouping', $short, $sub));
            $references = array_merge($references, self::contextReferences($statement->context, 'category', $short, $sub));
            $references = array_merge($references, self::contextReferences($statement->context, 'other', $short, $sub));
        }
                
        if (isset($statement->object->objectType) && $statement->object->objectType == 'SubStatement') {
            $references = array_merge($references, self::references($statement->object, $short, true));
        }

        return array_values($references);
    }

    /**
     * Extract the activities from a context location.
     *
     * @param  object  $context
     * @param  string  $location
     * @param  bool  $short
     * @param  bool  $sub
     * @return array
     */
    public static function contextReferences($context, string $location, bool $short = true, bool $sub = false): array
    {
        if (!isset($context->contextActivities) || !isset($context->contextActivities->$location)) {
            return [];
        }

        $references = [];
        foreach ($context->contextActivities->$location as $activity) {
            $references[] = self::reference($activity, $location, $short, $sub);
        }
        return $references;
    }

    /**
     * Format a reference.
     *
     * @param  object  $activity
     * @param  string  $location
     * @param  bool  $short
     * @param  bool  $sub
     * @return array
     */
    public static function reference(object $activity, string $location, bool $short = true, bool $sub = false): array
    {
        $ref = [
            'id' => self::hashId($activity->id),
            'location' => $location,
            'sub' => $sub,
        ];
        if (!$short) {
            $ref['iri'] = $activity->id;
            if (isset($activity->definition)) {
                $ref['definition'] = $activity->definition;
            }
        }
        return $ref;
    }
}
