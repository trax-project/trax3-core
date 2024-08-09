<?php

namespace Trax\Framework\Xapi\Helpers;

class XapiVerb
{
    /**
     * Generate hash for the given verb id.
     *
     * @param  string  $verbId
     * @return string
     */
    public static function hashId(string $verbId)
    {
        return HashId::create($verbId);
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

        $references = [
            self::reference($statement->verb, $short, $sub)
        ];
                
        if (isset($statement->object->objectType) && $statement->object->objectType == 'SubStatement') {
            $references = array_merge($references, self::references($statement->object, $short, true));
        }

        return array_values($references);
    }

    /**
     * Format a reference.
     *
     * @param  object  $verb
     * @param  bool  $short
     * @param  bool  $sub
     * @return array
     */
    public static function reference(object $verb, bool $short = true, bool $sub = false): array
    {
        $ref = [
            'id' => self::hashId($verb->id),
            'sub' => $sub,
        ];
        if (!$short) {
            $ref['iri'] = $verb->id;
        }
        return $ref;
    }
}
