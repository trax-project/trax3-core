<?php

namespace Trax\Framework\Xapi\Helpers;

class XapiObject
{
    /**
     * Generate hash for the given object.
     *
     * @param  object|array|string  $object
     * @return string|null
     *
     * @throws \Exception
     */
    public static function hashId($object)
    {
        // Object as an object.
        $object = Json::object($object);

        if (!isset($object->objectType) || $object->objectType == 'Activity') {
            return XapiActivity::hashId($object->id);
        } elseif ($object->objectType == 'Agent' || $object->objectType == 'Group') {
            return XapiAgent::hashId($object);
        } elseif ($object->objectType == 'StatementRef') {
            return $object->id;
        } elseif ($object->objectType == 'SubStatement') {
            return null;
        } else {
            // This is not possible with a valid statement.
            throw new \Exception('Invalid objectType found by XapiObject::hashId()');
        }
    }
}
