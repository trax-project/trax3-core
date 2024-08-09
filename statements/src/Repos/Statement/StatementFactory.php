<?php

namespace Trax\Statements\Repos\Statement;

use Trax\Framework\Repo\ModelFactory;
use Trax\Framework\Context;
use Trax\Framework\Xapi\Helpers\XapiAgent;
use Trax\Framework\Xapi\Helpers\XapiVerb;
use Trax\Framework\Xapi\Helpers\XapiActivity;
use Trax\Framework\Xapi\Helpers\XapiObject;
use Trax\Framework\Xapi\Helpers\XapiType;
use Trax\Framework\Xapi\Helpers\Json;
use Trax\Framework\Xapi\Helpers\StdClass;

class StatementFactory extends ModelFactory
{
    /**
     * Return the model class.
     *
     * @return string
     */
    public function modelClass(): string
    {
        return Statement::class;
    }

    /**
     * Return the unique columns used to upsert data.
     *
     * @return array
     */
    public function uniqueColumns(): array
    {
        return config('database.connections.statements.timeseries')
            ? ['id', 'store', 'stored']
            : ['id', 'store'];
    }

    /**
     * Prepare data before recording (used for bulk insert).
     *
     * @param  array  $data
     * @return array
     */
    public function prepare(array $data)
    {
        // prepareXapiStatement must be used to insert new statements.
        // This function is only used to update some status such as `validated` or `pseudonymized`.
        // It is also used during imports, but the data comes from prepareXapiStatement.
        // We assume that all the attributes are provided.

        // Be sure to have the right encoding.
        $data['raw'] = Json::encode($data['raw'], $this->jsonEncode);
        $data['agent_ids'] = Json::encode($data['agent_ids'], $this->jsonEncode);
        $data['activity_ids'] = Json::encode($data['activity_ids'], $this->jsonEncode);

        return $data;
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
        // Export directly the statement.
        // This includes the ID and stored.
        if ($rawFormat) {
            return $this->removeRawAttachments($model->raw);
        }

        // ID is required by the exporter in order to create DB records.
        // Data partitioning may be helpful.
        // Neither status, relations nor extracts attibutes are exported. They exist only for TRAX.
        // Pseudonymized is an important information to understand the raw statement so it is exported.
        // Stored is required by the exporter in order to keep the last timestamp.
        $data = StdClass::only($model, [
            'id', 'pseudonymized', 'store', 'client', 'stored',
        ]);
        
        $data['statement'] = $this->removeRawAttachments($model->raw);

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
        // Item may be a record coming from a previous export, or directly a raw statement.
        // We don't prepare the record with the prepareXapiStatement method, as we do with other services,
        // because the statement recorder needs only raw statements, and not statement records.
        return isset($item->statement)
            ? $this->removeRawAttachments($item->statement)
            : $this->removeRawAttachments($item);
    }

    /**
     * Remove raw attachments from a statement.
     *
     * @param  array|object|string  $statements
     * @return  array
     */
    public function removeRawAttachments($statement): array
    {
        // Be sure to work with arrays.
        $statement = Json::array($statement);

        if (isset($statement['attachments'])) {
            $statement['attachments'] = array_filter($statement['attachments'], function ($attachment) {
                return isset($attachment['fileUrl']);
            });
            if (count($statement['attachments']) == 0) {
                unset($statement['attachments']);
            }
        }
        return $statement;
    }

    /**
     * Prepare data before recording (used for bulk insert).
     * This function differs from `prepare` because it take an xAPI statement in parameter.
     *
     * @param  string|object|array  $statement
     * @return array
     */
    public function prepareXapiStatement($statement)
    {
        $pipeline = Context::pipeline();

        // Be sure to work with objects.
        $statement = Json::object($statement);
        
        // Set the ID.
        if (!isset($statement->id)) {
            $statement->id = traxUuid();
        }

        // Set the dates.
        $statement->stored = traxIsoNow();
        if (!isset($statement->timestamp)) {
            $statement->timestamp = $statement->stored;
        }

        // Set the authority.
        $statement->authority = $pipeline->authority;

        // Set the version.
        if (!isset($statement->version)) {
            $statement->version = '1.0.0';
        }

        // Normalize context activities.
        if (isset($statement->context) && isset($statement->context->contextActivities)) {
            $this->normalizeStatementContextActivities($statement->context->contextActivities);
        }
        if (isset($statement->object->objectType)
            && $statement->object->objectType == 'SubStatement'
            && isset($statement->object->context)
            && isset($statement->object->context->contextActivities)
        ) {
            $this->normalizeStatementContextActivities($statement->object->context->contextActivities);
        }

        // Return the record.
        return [
            'id' => $statement->id,
            'raw' => Json::encode($statement, $this->jsonEncode),
            'voided' => false,
            'voiding' => ($statement->verb->id == 'http://adlnet.gov/expapi/verbs/voided'),
            'validated' => $pipeline->validate_statements,
            'valid' => $pipeline->validate_statements,
            'pseudonymized' => XapiAgent::isPseudo($statement->actor),
            'actor_id' => XapiAgent::hashId($statement->actor),
            'verb_id' => XapiVerb::hashId($statement->verb->id),
            'object_id' => XapiObject::hashId($statement->object),
            'type_id' => isset($statement->object->definition) && isset($statement->object->definition->type) ? XapiType::hashId($statement->object->definition->type) : null,
            'agent_ids' => Json::encode(XapiAgent::referenceHids($statement), $this->jsonEncode),
            'activity_ids' => Json::encode(XapiActivity::referenceHids($statement), $this->jsonEncode),
            'registration' => isset($statement->context->registration) ? $statement->context->registration : null,
            'statement_ref' => isset($statement->object->objectType) && $statement->object->objectType == 'StatementRef' ? $statement->object->id : null,
            'store' => Context::store(),
            'client' => Context::client(),
            'stored' => $statement->stored,
            'timestamp' => $statement->timestamp,
        ];
    }
    
    /**
     * Normalize context activities.
     *
     * @param  object  $contextActivities
     * @return void
     */
    protected static function normalizeStatementContextActivities(object $contextActivities)
    {
        if (isset($contextActivities->parent) && !is_array($contextActivities->parent)) {
            $contextActivities->parent = [$contextActivities->parent];
        }
        if (isset($contextActivities->grouping) && !is_array($contextActivities->grouping)) {
            $contextActivities->grouping = [$contextActivities->grouping];
        }
        if (isset($contextActivities->category) && !is_array($contextActivities->category)) {
            $contextActivities->category = [$contextActivities->category];
        }
        if (isset($contextActivities->other) && !is_array($contextActivities->other)) {
            $contextActivities->other = [$contextActivities->other];
        }
    }
}
