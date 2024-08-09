<?php

namespace Trax\Framework\Xapi\Helpers;

class LangString
{
    /**
     * Return a string value of a JSON lang string.
     *
     * @param  object|array|string  $langString
     * @return string
     */
    public static function string($langString): string
    {
        if (is_string($langString)) {
            $langString = json_decode($langString, true);
        } elseif (!is_array($langString)) {
            $langString = json_decode(json_encode($langString), true);
        }
        return implode(' ', $langString);
    }
}
