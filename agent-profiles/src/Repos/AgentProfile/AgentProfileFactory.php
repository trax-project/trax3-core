<?php

namespace Trax\AgentProfiles\Repos\AgentProfile;

use Trax\Framework\Repo\ModelFactory;
use Trax\Framework\Context;
use Trax\Framework\Xapi\Helpers\Json;
use Trax\Framework\Xapi\Helpers\XapiAgent;
use Trax\Framework\Xapi\Helpers\XapiDocument;
use Trax\Framework\Xapi\Helpers\StdClass;

class AgentProfileFactory extends ModelFactory
{
    /**
     * Return the model class.
     *
     * @return string
     */
    public function modelClass(): string
    {
        return AgentProfile::class;
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
     * Prepare data before recording (used for bulk insert).
     *
     * @param  array  $data
     * @return array
     */
    public function prepare(array $data)
    {
        $now = traxIsoNow();
        
        $documentId = isset($data['id'])
            ? $data['id']
            : XapiDocument::agentProfileHashId(
                $data['agent'],
                $data['profile_id']
            );

        return [
            'id' => $documentId,
            'agent_id' => isset($data['agent']) ? XapiAgent::hashId($data['agent']) : $data['agent_id'],
            'agent_sid' => isset($data['agent']) ? XapiAgent::stringId($data['agent']) : $data['agent_sid'],
            'profile_id' => $data['profile_id'],
            'content' => $data['content'],
            'content_type' => $data['content_type'],
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
            'id', 'profile_id', 'content', 'content_type', 'store', 'stored', 'updated',
        ]);

        $data['agent'] = XapiAgent::jsonId($model->agent_sid);

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
        // The import process will use the `prepare` method which requires the following attributes.
        return [
            'agent' => $item->agent,
            'profile_id' => $item->profile_id,
            'content' => $item->content,
            'content_type' => $item->content_type,
        ];
    }

    /**
     * Update an existing model instance, given some data.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $data
     * @param  bool  $mergeContent
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($model, array $data, $mergeContent = false)
    {
        if ($mergeContent) {
            $data['content'] = json_encode(
                Json::merge($model->content, $data['content'], 1)
            );
        }
        $data['updated'] = traxIsoNow();
        $model->fill($data);
        return $model;
    }
}
