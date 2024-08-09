<?php

namespace Trax\Framework\Xapi\Helpers;

class StdClass
{
    /**
     * Return an associative array extracting some properties of a given object.
     *
     * @param  mixed  $object
     * @param  array  $props
     * @return array
     */
    public static function only($object, array $props = []): array
    {
        if (empty($props)) {
            return (array) $object;
        }

        $res = [];
        foreach ($props as $prop) {
            $res[$prop] = $object->$prop;
        }
        return $res;
    }
}
