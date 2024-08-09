<?php

namespace Trax\Framework\Xapi\Helpers;

class XapiAttachment
{
    /**
     * Extract all the attachments of a collection of statements.
     *
     * @param  \Illuminate\Support\Collection  $statements
     * @return \Illuminate\Support\Collection
     */
    public static function sha2sFromStatements($statements)
    {
        return $statements->reduce(function (array $sha2s, $statement) {
            if (isset($statement->raw->attachments)) {
                $newSha2s = collect($statement->raw->attachments)->pluck('sha2')->all();
                return array_merge($sha2s, $newSha2s);
            }
            return $sha2s;
        }, []);
    }
}
