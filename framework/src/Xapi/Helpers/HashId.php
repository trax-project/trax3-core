<?php

namespace Trax\Framework\Xapi\Helpers;

class HashId
{
    /**
     * Generate hash for the given activity.
     *
     * @param  string  $id
     * @return string
     *
     * @throw \Exception
     */
    public static function create(string $id)
    {
        return hash('xxh128', $id);
    }
}
