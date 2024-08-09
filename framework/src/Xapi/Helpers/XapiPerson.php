<?php

namespace Trax\Framework\Xapi\Helpers;

class XapiPerson
{
    /**
     * Generate a person with a given array of agents.
     *
     * @param  array  $agents  Each agent has an array form
     * @param  bool  $asObject
     * @return array|object|null
     */
    public static function make(array $agents, bool $asObject = false)
    {
        $person = [
            'objectType' => 'Person'
        ];

        foreach ($agents as $agent) {
            // Remove the type.
            if (isset($agent['objectType'])) {
                unset($agent['objectType']);
            }

            // Aggregate the other props.
            foreach ($agent as $prop => $value) {
                if (!isset($person[$prop])) {
                    $person[$prop] = [];
                }
                $person[$prop][] = $value;
            }
        }

        return $asObject ? json_decode(json_encode($person)) : $person;
    }
}
