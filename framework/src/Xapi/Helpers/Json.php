<?php

namespace Trax\Framework\Xapi\Helpers;

class Json
{
    /**
     * Return a JSON value with an array form.
     *
     * @param  object|array|string|null  $value
     * @return array|null
     */
    public static function array($value = null): array
    {
        if (empty($value)) {
            return [];
        }
        if (is_string($value)) {
            return json_decode($value, true);
        }
        if (!is_array($value)) {
            return json_decode(json_encode($value), true);
        }
        return $value;
    }

    /**
     * Return a JSON value with an object form.
     *
     * @param  object|array|string|null  $value
     * @return object|null
     */
    public static function object($value = null)
    {
        if (empty($value)) {
            return (object)[];
        }
        if (is_string($value)) {
            return json_decode($value);
        }
        if (is_array($value)) {
            return json_decode(json_encode($value));
        }
        return $value;
    }

    /**
     * Return the value of a DB JSON field to be inserted.
     *
     * @param  string|object|array|null  $json
     * @param  bool  $encode
     * @return mixed
     */
    public static function encode($json = null, bool $encode = true)
    {
        // Act as a confirmation to encode. Convenience for callers.
        if (!$encode) {
            return $json;
        }
        if (empty($json)) {
            return '{}';
        }
        if (is_string($json)) {
            return $json;
        }
        return json_encode($json);
    }

    /**
     * Merge 2 given JSON objects and return the merged JSON object.
     *
     * @param  object|array|string  $json1
     * @param  object|array|string  $json2
     * @param  int  $depthLimit
     * @return object
     */
    public static function merge($json1, $json2, int $depthLimit = 100)
    {
        // Be sure to work with objects.
        $json1 = self::object($json1);
        $json2 = self::object($json2);

        // Merge all props.
        $props = get_object_vars($json2);
        foreach ($props as $prop => $value) {
            if (isset($json1->$prop) && is_object($value) && $depthLimit > 1) {
                $json1->$prop = self::merge($json1->$prop, $value, $depthLimit - 1);
            } else {
                $json1->$prop = $value;
            }
        }
        return $json1;
    }

    /**
     * Generate a hash for a given JSON.
     *
     * @param  object|array|string  $json
     * @return string
     */
    public static function hash($json): string
    {
        // Be sure to work with an array.
        $json = Json::array($json);

        // Sort the array keys recursively.
        self::sort($json);
        
        return hash('xxh64', json_encode($json));
    }

    /**
     * Sort the array form of a JSON by keys and recursively.
     *
     * @param  array  $json
     * @return void
     */
    public static function sort(array &$json): void
    {
        foreach ($json as &$value) {
            if (is_array($value)) {
                self::sort($value);
            }
        }
        ksort($json);
    }
}
