<?php

namespace Trax\Agents\Repos\Agent;

use Trax\Framework\Repo\ModelFactory;
use Trax\Framework\Context;
use Trax\Framework\Xapi\Helpers\XapiAgent;
use Trax\Framework\Xapi\Helpers\Json;
use Trax\Framework\Xapi\Helpers\StdClass;

class AgentFactory extends ModelFactory
{
    /**
     * Return the model class.
     *
     * @return string
     */
    public function modelClass(): string
    {
        return Agent::class;
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
        // prepareXapiAgent must be used to insert directly new xAPI agents (e.g. tests).
        // This function is used to update agent definitions.
        // It is also used during imports, but the data comes from prepareXapiActivity.
        if (isset($data['sid'])) {
            list($type, $field1, $field2) = XapiAgent::sidFields($data['sid']);
        } else {
            $type = $data['sid_type'];
            $field1 = $data['sid_field_1'];
            $field2 = $data['sid_field_2'];
        }

        $membersHids = isset($data['members']) ? Json::array($data['members']) : [];

        $now = traxIsoNow();

        return [
            'id' => $data['id'],
            'sid_type' => $type,
            'sid_field_1' => $field1,
            'sid_field_2' => $field2,
            'name' => isset($data['name']) ? $data['name'] : null,
            'is_group' => isset($data['is_group']) ? $data['is_group'] : false,
            'members' =>  Json::encode($membersHids, $this->jsonEncode),
            'members_count' =>  count($membersHids),
            'pseudonymized' => isset($data['pseudonymized']) ? $data['pseudonymized'] : false,
            'person_id' => isset($data['person_id']) ? $data['person_id'] : traxUuid(),
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

        $data['agent'] = XapiAgent::jsonByModel($model);

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
        return $this->prepareXapiAgent($item->agent);
    }

    /**
     * Prepare data before recording (used for bulk insert).
     * This function differs from `prepare` because it takes an xAPI agent in parameter.
     *
     * @param  string|object|array  $agent
     * @return array
     */
    public function prepareXapiAgent($agent)
    {
        // Be sure to work with objects.
        $agent = Json::object($agent);
        
        $membersHids = XapiAgent::memberReferenceHids($agent);

        $now = traxIsoNow();

        return [
            'id' => XapiAgent::hashId($agent),
            'sid_type' => XapiAgent::sidType($agent),
            'sid_field_1' => XapiAgent::sidField1($agent),
            'sid_field_2' => XapiAgent::sidField2($agent),
            'name' => isset($agent->name) ? $agent->name : null,
            'is_group' => isset($agent->objectType) && $agent->objectType == 'Group',
            'members' =>  Json::encode($membersHids, $this->jsonEncode),
            'members_count' =>  count($membersHids),
            'pseudonymized' => false,
            'person_id' => traxUuid(),
            'store' => Context::store(),
            'stored' => $now,
            'updated' => $now,
        ];
    }
}
