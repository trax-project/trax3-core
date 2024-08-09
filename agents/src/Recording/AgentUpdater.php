<?php

namespace Trax\Agents\Recording;

use Illuminate\Support\Collection;
use Trax\Framework\Context;
use Trax\Framework\Service\Facades\EventManager;
use Trax\Framework\Xapi\Helpers\Json;
use Trax\Framework\Xapi\Helpers\XapiAgent;
use Trax\Agents\Repos\Agent\AgentRepository;
use Trax\Agents\Events\AgentsUpdated;

class AgentUpdater
{
    /**
     * @var \Trax\Agents\Repos\Agent\AgentRepository
     */
    protected $agents;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->agents = app(AgentRepository::class);
    }

    /**
     * Update the agents of a collection of statements.
     * Each statement takes the form of an array of properties as it has been inserted into the database.
     *
     * @param   \Illuminate\Support\Collection|array  $statements
     * @return void
     */
    public function update($statements): void
    {
        $pipeline = Context::pipeline();
        
        if (!$pipeline->update_agents) {
            return;
        }

        // Update agents.
        $records = $this->updateStatementsAgents($statements);

        // Dispatch events.
        if (!$records->isEmpty()) {
            EventManager::dispatch(AgentsUpdated::class, $records);
        }
    }

    /**
     * Update the agents of a collection of statements.
     * Each statement takes the form of an array of properties as it has been inserted into the database.
     *
     * @param   \Illuminate\Support\Collection|array  $statements
     * @return \Illuminate\Support\Collection
     */
    protected function updateStatementsAgents($statements): Collection
    {
        // Be sure to work with a collection.
        if (is_array($statements)) {
            $statements = collect($statements);
        }

        // Extract agents and keep the last infos from the batch.
        $incoming = $this->agentsInfo($statements);

        // Exit if nothing to update.
        if ($incoming->isEmpty()) {
            return $incoming;
        }

        // Update the definitions.
        $incoming = $this->updateDefinitions($incoming);

        // Update the agents table.
        return $this->insertOrUpdateAgents($incoming);
    }

    /**
     * Extract agents from a collection of statements.
     *
     * @param   \Illuminate\Support\Collection  $statements
     * @return \Illuminate\Support\Collection
     */
    protected function agentsInfo(Collection $statements): Collection
    {
        // We collect all the agents, and we group them by their id.
        return collect($statements->reduce(function ($info, $statement) {
            return $info->merge(
                collect(XapiAgent::references(is_array($statement) ? $statement['raw'] : $statement->raw, false))
            );
        }, collect()))->groupBy('id')

        // For each group, we replace the name and the members props.
        ->map(function ($items, $id) {
            $updated = $items->last();

            // Name.
            $updated['name'] = $items->reduce(function ($lastName, $item) {
                return isset($item['name']) ? $item['name'] : $lastName;
            }, '');
            if (empty($updated['name'])) {
                unset($updated['name']);
            }

            // Member.
            $updated['members'] = $items->reduce(function ($lastMember, $item) {
                return isset($item['members']) ? $item['members'] : $lastMember;
            }, []);
            if (empty($updated['members'])) {
                unset($updated['members']);
            }

            return $updated;
        });
    }

    /**
     * Update definitions.
     *
     * @param  \Illuminate\Support\Collection  $incoming
     * @return \Illuminate\Support\Collection
     */
    protected function updateDefinitions(Collection $incoming): Collection
    {
        // Get the existing agents.
        $existing = $this->agents->in($incoming->pluck('id'));

        // Update incoming infos.
        return $incoming->map(function ($info) use ($existing) {
            if ($model = $existing->where('id', $info['id'])->first()) {
                // Name.
                if (!isset($info['name']) && isset($model->name)) {
                    $info['name'] = $model->name;
                }

                // Members.
                $members = Json::array($model->members);
                if (!isset($info['members']) && !empty($members)) {
                    $info['members'] = $members;
                }

                // Other info to preserve.
                $info['pseudonymized'] = $model->pseudonymized;
                $info['person_id'] = $model->person_id;
                $info['stored'] = $model->stored;
            }
            return $info;
        });
    }

    /**
     * Insert or update agents.
     *
     * @param  \Illuminate\Support\Collection  $incoming
     * @return \Illuminate\Support\Collection
     */
    protected function insertOrUpdateAgents(Collection $incoming): Collection
    {
        $agents = $incoming->map(function ($info) {
            $agent = [
                'id' => $info['id'],
                'sid' => $info['sid'],
                'is_group' => $info['is_group'],
            ];
            if (isset($info['name'])) {
                $agent['name'] = $info['name'];
            }
            // Don't add members if it is not marked as a Group.
            // This situation may happen if an ID is first used for a Group, then for an Agent.
            if ($info['is_group'] && isset($info['members'])) {
                $agent['members'] = $info['members'];
            }
            if (isset($info['pseudonymized'])) {
                $agent['pseudonymized'] = $info['pseudonymized'];
            }
            if (isset($info['person_id'])) {
                $agent['person_id'] = $info['person_id'];
            }
            if (isset($info['stored'])) {
                $agent['stored'] = $info['stored'];
            }
            return $agent;
        })->toArray();

        return collect(
            $this->agents->upsert($agents)
        );
    }
}
