<?php

namespace Trax\Statements\Recording\Actions;

use Illuminate\Support\Collection;
use Trax\Framework\Xapi\Exceptions\XapiConflictException;
use Trax\Statements\Repos\Statement\StatementRepository;

trait ManageStatementIds
{
    /**
     * Manage statement IDs.
     *
     * @param  \Illuminate\Support\Collection  $statements
     * @param  bool  $checkConflicts
     * @return \Illuminate\Support\Collection
     */
    public function manageStatementIds(Collection $statements, bool $checkConflicts = true): Collection
    {
        // Check ID conflicts.
        if ($checkConflicts) {
            $existing = app(StatementRepository::class)->in(
                $statements->pluck('id')->filter()
            );
            if (!$existing->isEmpty()) {
                throw new XapiConflictException("Statement(s) with similar ID already exist in the store.");
            }
        }

        // Be sure to have an ID for each statement.
        return $statements->map(function ($statement) {
            if (!isset($statement->id)) {
                $statement->id = traxUuid();
            }
            return $statement;
        });
    }
}
