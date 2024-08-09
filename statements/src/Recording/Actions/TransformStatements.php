<?php

namespace Trax\Statements\Recording\Actions;

use Illuminate\Support\Collection;

trait TransformStatements
{
    /**
     * Transform statements.
     *
     * @param  \Illuminate\Support\Collection  $statements
     * @return \Illuminate\Support\Collection
     */
    public function transformStatements(Collection $statements): Collection
    {
        return $statements;
    }
}
