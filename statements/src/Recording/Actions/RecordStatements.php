<?php

namespace Trax\Statements\Recording\Actions;

use Illuminate\Support\Collection;
use Trax\Statements\Repos\Statement\StatementRepository;

trait RecordStatements
{
    /**
     * Record statements.
     *
     * @param  \Illuminate\Support\Collection  $statements
     * @return \Illuminate\Support\Collection
     */
    protected function recordStatements(Collection $statements): Collection
    {
        return collect(
            app(StatementRepository::class)->upsert($statements->all(), 'prepareXapiStatement')
        );
    }
}
