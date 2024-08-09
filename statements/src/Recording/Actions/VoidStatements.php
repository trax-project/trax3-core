<?php

namespace Trax\Statements\Recording\Actions;

use Illuminate\Support\Collection;
use Trax\Statements\Repos\Statement\StatementRepository;

trait VoidStatements
{
    /**
     * Void statements.
     *
     * @param  \Illuminate\Support\Collection  $statements
     * @return \Illuminate\Support\Collection
     */
    public function voidStatements(Collection $statements): Collection
    {
        $targetIds = $statements->where('verb.id', 'http://adlnet.gov/expapi/verbs/voided')->pluck('object.id')->all();

        app(StatementRepository::class)->updateByIds($targetIds, ['voided' => true]);

        return collect($targetIds);
    }
}
