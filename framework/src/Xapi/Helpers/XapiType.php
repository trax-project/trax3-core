<?php

namespace Trax\Framework\Xapi\Helpers;

class XapiType
{
    /**
     * Generate hash for the given verb id.
     *
     * @param  string  $iri
     * @return string
     */
    public static function hashId(string $iri)
    {
        return HashId::create($iri);
    }
}
