<?php

namespace Trax\Framework\Database;

interface DataRecorder
{
    /**
     * Record data.
     *
     * @param  array  $records
     * @return array
     */
    public function record(array $records): array;
}
