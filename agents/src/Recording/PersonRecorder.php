<?php

namespace Trax\Agents\Recording;

use Trax\Framework\Xapi\Helpers\Json;
use Trax\Framework\Xapi\Helpers\XapiAgent;
use Trax\Framework\Xapi\Schema\Agent;
use Trax\Framework\Exceptions\SimpleException;
use Trax\Agents\Repos\Agent\AgentRepository;

class PersonRecorder
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
     * Validate persons.
     *
     * @param  array|null  $persons
     * @return void
     *
     * @throws \Trax\Framework\Exceptions\SimpleException
     */
    public function validate($persons): void
    {
        if (!is_array($persons)) {
            throw new SimpleException('The body must be a JSON array contained persons.');
        }
        foreach ($persons as $person) {
            if (!is_array($person)) {
                throw new SimpleException('Each item of the top array must be an array of agents.');
            }
            foreach ($person as $agent) {
                if (!is_object($agent)) {
                    throw new SimpleException('Each provided agent must be a JSON object.');
                }
                if (!Agent::isValid($agent)) {
                    throw new SimpleException('Each provided agent must be a valid xAPI agent (not a group).');
                }
            }
        }
    }
    
    /**
     * Record persons.
     *
     * @param  array  $persons
     * @return void
     */
    public function record(array $persons): void
    {
        // Get the existing agents.
        $agentIds = collect($persons)->reduce(function ($hashIds, $person) {
            return $hashIds->concat(
                collect($person)->map(function ($agent) {
                    return XapiAgent::hashId($agent);
                })
            );
        }, collect([]))->unique();

        $existing = $this->agents->in($agentIds);

        // Prepare the list of agents to upsert.
        $agents = [];
        foreach ($persons as $person) {
            $personId = traxUuid();

            foreach ($person as $agent) {
                $agentId = XapiAgent::hashId($agent);

                if ($model = $existing->where('id', $agentId)->first()) {
                    // Existing agent.
                    $agentRecord = [
                        'id' => $model->id,
                        'sid' => XapiAgent::stringIdByModel($model),
                        'is_group' => $model->is_group,
                        'name' => $model->name,
                        'members' => Json::array($model->members),
                        'pseudonymized' => $model->pseudonymized,
                        'person_id' => $personId,
                        'stored' => $model->stored,
                    ];
                } else {
                    // New agent.
                    $agentRecord = [
                        'id' => $agentId,
                        'sid' => XapiAgent::stringId($agent),
                        'is_group' => XapiAgent::isGroup($agent),
                        'pseudonymized' => XapiAgent::isPseudo($agent),
                        'person_id' => $personId,
                    ];
                }
                $agents[] = $agentRecord;
            }
        }

        // Upsert the updated agents.
        $this->agents->upsert($agents);
    }
}
