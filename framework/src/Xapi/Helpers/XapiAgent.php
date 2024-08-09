<?php

namespace Trax\Framework\Xapi\Helpers;

class XapiAgent
{
    /**
     * Is the agent a group?
     *
     * @param  object|array|string  $agent
     * @return bool
     */
    public static function isGroup($agent)
    {
        // Agent as an object.
        $agent = Json::object($agent);

        return isset($agent->objectType) && $agent->objectType == 'Group';
    }

    /**
     * Is the agent pseudonymized?
     *
     * @param  object|array|string  $agent
     * @return bool
     */
    public static function isPseudo($agent)
    {
        // Agent as an object.
        $agent = Json::object($agent);

        if (isset($agent->account) && $agent->account->homePage == config('trax.xapi.pseudo.homepage')) {
            return true;
        }

        if (isset($agent->mbox) || isset($agent->mbox_sha1sum) || isset($agent->openid)) {
            return false;
        }

        if (isset($agent->member) && isset($agent->member[0]->account) && $agent->member[0]->account->homePage == config('trax.xapi.pseudo.homepage')) {
            return true;
        }

        return false;
    }

    /**
     * Generate hash for the given agent.
     *
     * @param  object|array|string  $agent
     * @return string|null
     */
    public static function hashId($agent)
    {
        return self::hashIdFromStringId(
            self::stringId($agent)
        );
    }

    /**
     * Generate hash for the given agent.
     *
     * @param  string|null  $stringId
     * @return string|null
     */
    public static function hashIdFromStringId($stringId)
    {
        // The stringId may be null with anonymous groups.
        return is_null($stringId) ? null : HashId::create($stringId);
    }

    /**
     * Validate the format of a string ID.
     *
     * @param  string  $sid
     * @return string|null
     */
    public static function isStringId(string $sid)
    {
        $parts = explode('::', $sid);
        
        if (count($parts) < 2) {
            return false;
        }
        
        if (!in_array($parts[0], ['mbox', 'mbox_sha1sum', 'openid', 'account'])) {
            return false;
        }

        // We don't check the ID itself.
        return true;
    }

    /**
     * Generate a stringified ID for a given agent.
     *
     * @param  object|array|string  $agent
     * @return string|null
     */
    public static function stringId($agent)
    {
        // Agent as an object.
        $agent = Json::object($agent);

        if (is_null($agent)) {
            return null;
        }

        if (isset($agent->mbox)) {
            return "mbox::$agent->mbox";
        } elseif (isset($agent->mbox_sha1sum)) {
            return "mbox_sha1sum::$agent->mbox_sha1sum";
        } elseif (isset($agent->openid)) {
            return "openid::$agent->openid";
        } elseif (isset($agent->account)
            && is_object($agent->account)
            && isset($agent->account->name)
            && isset($agent->account->homePage)
        ) {
            return 'account::'.$agent->account->name.'@'.$agent->account->homePage;
        } else {
            // Anonymous group.
            return null;
        }
    }

    /**
     * Generate a stringified ID for a given agent.
     *
     * @param  object|\Trax\Agents\Repos\Agent\Agent  $model
     * @return object|array
     */
    public static function stringIdByModel($model)
    {
        return self::stringId(
            self::jsonIdByModel($model)
        );
    }

    /**
     * Return the fields of an sid.
     *
     * @param  string  $sid
     * @return array
     */
    public static function sidFields(string $sid)
    {
        list($type, $rest) = explode('::', $sid);
        if ($type == 'account') {
            list($field1, $field2) = explode('@', $rest);
        } else {
            $field1 = $rest;
            $field2 = null;
        }
        return [$type, $field1, $field2];
    }

    /**
     * Return the type of ID.
     *
     * @param  object|array|string  $agent
     * @return string|null
     */
    public static function sidType($agent)
    {
        // Agent as an object.
        $agent = Json::object($agent);

        if (isset($agent->mbox)) {
            return 'mbox';
        } elseif (isset($agent->mbox_sha1sum)) {
            return 'mbox_sha1sum';
        } elseif (isset($agent->openid)) {
            return 'openid';
        } elseif (isset($agent->account)) {
            return 'account';
        } else {
            // Anonymous group.
            return null;
        }
    }

    /**
     * Return the field 1 of ID.
     *
     * @param  object|array|string  $agent
     * @return string|null
     */
    public static function sidField1($agent)
    {
        // Agent as an object.
        $agent = Json::object($agent);

        if (isset($agent->mbox)) {
            return $agent->mbox;
        } elseif (isset($agent->mbox_sha1sum)) {
            return $agent->mbox_sha1sum;
        } elseif (isset($agent->openid)) {
            return $agent->openid;
        } elseif (isset($agent->account)) {
            return $agent->account->name;
        }
        // Anonymous group.
        return null;
    }

    /**
     * Return the field 2 of ID.
     *
     * @param  object|array|string  $agent
     * @return string|null
     */
    public static function sidField2($agent)
    {
        // Agent as an object.
        $agent = Json::object($agent);

        if (isset($agent->account)) {
            return $agent->account->homePage;
        }
        return null;
    }

    /**
     * Get the agent object or array from a given stringified ID.
     *
     * @param  string  $stringId
     * @param  bool  $asObject
     * @return array|object|null
     */
    public static function jsonId(string $stringId, bool $asObject = false)
    {
        list($type, $value) = explode('::', $stringId);
        if ($type == 'mbox') {
            $agent = ['mbox' => $value];
        } elseif ($type == 'mbox_sha1sum') {
            $agent = ['mbox_sha1sum' => $value];
        } elseif ($type == 'openid') {
            $agent = ['openid' => $value];
        } elseif ($type == 'account') {
            list($name, $homePage) = explode('@', $value);
            $agent = ['account' => [
                'name' => $name,
                'homePage' => $homePage,
            ]];
        } else {
            return null;
        }
        return $asObject ? json_decode(json_encode($agent)) : $agent;
    }

    /**
     * Get the agent object or array from a given ID fields.
     *
     * @param  string  $type
     * @param  string  $field1
     * @param  string|null  $field2
     * @param  bool  $asObject
     * @return array|object|null
     */
    public static function jsonIdByFields(string $type, string $field1, $field2, bool $asObject = false)
    {
        if ($type == 'mbox') {
            $agent = ['mbox' => $field1];
        } elseif ($type == 'mbox_sha1sum') {
            $agent = ['mbox_sha1sum' => $field1];
        } elseif ($type == 'openid') {
            $agent = ['openid' => $field1];
        } elseif ($type == 'account') {
            $agent = ['account' => [
                'name' => $field1,
                'homePage' => $field2,
            ]];
        } else {
            return null;
        }
        return $asObject ? json_decode(json_encode($agent)) : $agent;
    }

    /**
     * Get the agent object or array from a given agent model.
     *
     * @param  object|\Trax\Agents\Repos\Agent\Agent  $model
     * @param  bool  $asObject
     * @return array|object|null
     */
    public static function jsonIdByModel($model, bool $asObject = false)
    {
        return self::jsonIdByFields(
            $model->sid_type,
            $model->sid_field_1,
            $model->sid_field_2,
            $asObject,
        );
    }

    /**
     * Get the agent object or array from a given agent model.
     *
     * @param  object|\Trax\Agents\Repos\Agent\Agent  $model
     * @param  bool  $asObject
     * @return array|object|null
     */
    public static function jsonByModel($model, bool $asObject = false)
    {
        $agent = [
            'objectType' => $model->is_group ? 'Group' : 'Agent'
        ];

        if (!is_null($model->name)) {
            $agent['name'] = $model->name;
        }

        foreach (self::jsonIdByModel($model) as $prop => $val) {
            $agent[$prop] = $val;
        }

        return $asObject ? json_decode(json_encode($agent)) : $agent;
    }

    /**
     * Generate pseudo name for a given string ID.
     *
     * @param  string  $sid
     * @return string
     */
    public static function pseudoName(string $sid)
    {
        return md5($sid . config('trax.xapi.pseudo.hash_key'));
    }

    /**
     * Extract all the agent HIDs of the statement.
     *
     * @param  object|array|string  $statement
     * @return array
     */
    public static function referenceHids($statement): array
    {
        return array_values(array_unique(array_map(function ($reference) {
            return $reference['id'];
        }, self::references($statement))));
    }

    /**
     * Pseudonymize all the agents of the statement.
     *
     * @param  object|array|string  $statement
     * @param  array  $pseudos
     * @return object
     */
    public static function pseudonymizeStatement($statement, array &$pseudos, bool $asObject = true): object
    {
        // Statement as an object.
        $statement = Json::object($statement);

        $statement->actor = self::pseudonymizeAgent($statement->actor, $pseudos);
        
        if (isset($statement->object->objectType) && in_array($statement->object->objectType, ['Agent', 'Group'])) {
            $statement->object = self::pseudonymizeAgent($statement->object, $pseudos);
        }
        
        if (isset($statement->context) && isset($statement->context->instructor)) {
            $statement->context->instructor = self::pseudonymizeAgent($statement->context->instructor, $pseudos);
        }
        
        if (isset($statement->context) && isset($statement->context->team)) {
            $statement->context->team = self::pseudonymizeAgent($statement->context->team, $pseudos);
        }
        
        if (isset($statement->object->objectType) && $statement->object->objectType == 'SubStatement') {
            $statement->object = self::pseudonymizeStatement($statement->object, $pseudos);
        }

        return $asObject ? $statement : json_decode(json_encode($statement), true);
    }

    /**
     * Pseudonymize all the agents of the statement.
     *
     * @param  object|array|string  $agent
     * @param  array  $pseudos
     * @return object
     */
    public static function pseudonymizeAgent($agent, array &$pseudos): object
    {
        // Agent as an object.
        $agent = Json::object($agent);
        $sid = self::stringId($agent);

        // Remove name.
        unset($agent->name);

        // Members.
        if (isset($agent->member)) {
            $agent->member = array_map(function ($member) use (&$pseudos) {
                return self::pseudonymizeAgent($member, $pseudos);
            }, $agent->member);
        }

        // Anonymous groups, no identification.
        if (is_null($sid)) {
            return $agent;
        }

        // Generate the pseudo name.
        if (!isset($pseudos[$sid])) {
            $pseudos[$sid] = self::pseudoName($sid);
        }

        // Set the account prop.
        $agent->account = (object)[
            'name' => $pseudos[$sid],
            'homePage' => config('trax.xapi.pseudo.homepage'),
        ];

        // Remove other identifications.
        unset($agent->mbox);
        unset($agent->mbox_sha1sum);
        unset($agent->openid);
        return $agent;
    }

    /**
     * Extract all the agents of the statement.
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

        $references = self::agentReferences($statement->actor, 'actor', $short, $sub);
        
        if (isset($statement->object->objectType) && in_array($statement->object->objectType, ['Agent', 'Group'])) {
            $references = array_merge($references, self::agentReferences($statement->object, 'object', $short, $sub));
        }
        
        if (isset($statement->context) && isset($statement->context->instructor)) {
            $references = array_merge($references, self::agentReferences($statement->context->instructor, 'instructor', $short, $sub));
        }
        
        if (isset($statement->context) && isset($statement->context->team)) {
            $references = array_merge($references, self::agentReferences($statement->context->team, 'team', $short, $sub));
        }
        
        if (isset($statement->authority)) {
            $references = array_merge($references, self::agentReferences($statement->authority, 'authority', $short, $sub));
        }
                
        if (isset($statement->object->objectType) && $statement->object->objectType == 'SubStatement') {
            $references = array_merge($references, self::references($statement->object, $short, true));
        }

        // Remove null HIDs. May happen with anonymous groups.
        $references = array_filter($references, function ($reference) {
            return isset($reference['id']);
        });

        return array_values($references);
    }

    /**
     * Extract the agent and its members from a location.
     *
     * @param  object  $agent
     * @param  string  $location
     * @param  bool  $short
     * @param  bool  $sub
     * @return array
     */
    public static function agentReferences($agent, string $location = 'actor', bool $short = true, bool $sub = false): array
    {
        $references = self::memberReferences($agent, $location, $short, $sub);
        $references[] = self::reference($agent, false, $location, $short, $sub);
        return $references;
    }

    /**
     * Extract the members from a location.
     *
     * @param  object  $agent
     * @param  string  $location
     * @param  bool  $short
     * @param  bool  $sub
     * @return array
     */
    public static function memberReferences($agent, string $location = 'actor', bool $short = true, bool $sub = false): array
    {
        $references = [];

        if (isset($agent->member)) {
            foreach ($agent->member as $member) {
                $references[] = self::reference($member, true, $location, $short, $sub);
            }
        }
        return $references;
    }

    /**
     * Format a reference.
     *
     * @param  object  $agent
     * @param  bool  $asMember
     * @param  string  $location
     * @param  bool  $short
     * @param  bool  $sub
     * @return array
     */
    public static function reference(object $agent, bool $asMember, string $location, bool $short = true, bool $sub = false): array
    {
        $ref = [
            'id' => self::hashId($agent),
            'location' => $location,
            'as_member' => $asMember,
            'sub' => $sub,
        ];
        if (!$short) {
            $ref['sid'] = self::stringId($agent);
            $ref['is_group'] = isset($agent->objectType) && $agent->objectType == 'Group';
            if (isset($agent->name)) {
                $ref['name'] = $agent->name;
            }
            if (isset($agent->member)) {
                $ref['members'] = self::memberReferenceHids($agent);
            }
        }
        return $ref;
    }

    /**
     * Extract all the member HIDs of the agent.
     *
     * @param  object  $agent
     * @return array
     */
    public static function memberReferenceHids($agent): array
    {
        return array_values(array_unique(array_map(function ($reference) {
            return $reference['id'];
        }, self::memberReferences($agent))));
    }
}
