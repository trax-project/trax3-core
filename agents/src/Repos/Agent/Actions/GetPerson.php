<?php

namespace Trax\Agents\Repos\Agent\Actions;

use Illuminate\Support\Collection;
use Trax\Framework\Xapi\Helpers\XapiPerson;
use Trax\Framework\Xapi\Helpers\XapiAgent;
use Trax\Framework\Xapi\Helpers\Json;

trait GetPerson
{
    /**
     * Get a person in the xAPI format.
     *
     * @param  array|null  $agent
     * @return array
     */
    public function getXapiPerson($agent): array
    {
        // Search for an existing agent.
        $resourse = $this->getOne($agent);

        if ($resourse) {
            // Get all the agents of the same person.
            $agents = $this->addFilter([
                'person_id' => $resourse->person_id
            ])->get()->map(function ($agent) {
                return XapiAgent::jsonByModel($agent);
            })->all();

            return XapiPerson::make($agents);
        } else {
            return XapiPerson::make([$agent]);
        }
    }

    /**
     * Get a person in a custom format, which is the list of JSON agents.
     *
     * @param  array  $agent
     * @return \Illuminate\Support\Collection
     */
    public function getPersonAgents($agent): Collection
    {
        // We need to disable the mine scope. Why?
        // For actors, we have a mine scope on the agents.
        // In order to get the other agents of the user (i.e. person), we need to bypass the mine scope.
        if ($resourse = $this->disableMineScope()->getOne($agent)) {
            return $this->addFilter([
                'person_id' => $resourse->person_id,
            ])->disableMineScope()->get()->map(function ($agent) {
                return XapiAgent::jsonByModel($agent, true);
            });
        } else {
            return collect([Json::object($agent)]);
        }
    }

    /**
     * Get a person in a custom format, which is the list of string IDs.
     *
     * @param  array  $agent
     * @return \Illuminate\Support\Collection
     */
    public function getPersonStringIds($agent): Collection
    {
        return $this->getPersonAgents($agent)->map(function ($agent) {
            return XapiAgent::stringId($agent);
        });
    }
}
