<?php

namespace Trax\Statements\Repos\Statement\Actions;

use Illuminate\Support\Collection;
use Trax\Framework\Repo\Query;
use Trax\Framework\Xapi\Schema\Statement as StatementSchema;
use Trax\Framework\Xapi\Helpers\Json;
use Trax\Framework\Repo\Actions\FinalizeTimestamps;

trait FinalizeStatements
{
    use FinalizeTimestamps;

    /**
     * Finalize the resources before returning them.
     *
     * @param  \Illuminate\Support\Collection  $resources
     * @param  \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Support\Collection
     */
    public function finalize(Collection $resources, Query $query = null): Collection
    {
        // Change the timesamps which may have a different format with PostgreSQL.
        $resources = $this->finalizeTimestamps($resources);

        // Nothing to do;
        if (!isset($query)) {
            return $resources;
        }
        if (!$query->option('rearrange', false) && !$query->hasOption('format')) {
            return $resources;
        }

        return $resources->map(function ($resource) use ($query) {
            // DB query builder may return json encoded data.
            $statement = Json::object($resource->raw);

            // Format.
            if ($query->hasOption('format')) {
                $statement = StatementSchema::format(
                    $statement,
                    $query->option('format', 'exact'),
                    $query->option('lang')
                );
            }
            
            // Reorder props for readability.
            if ($query->option('rearrange', false)) {
                $statement = StatementSchema::reorderStatement($statement);
            }

            // Result.
            $resource->raw = $statement;
            return $resource;
        });
    }
}
