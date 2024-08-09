<?php

namespace Trax\Statements\Recording\Actions;

use Trax\Framework\Xapi\Helpers\XapiAgent;

trait PseudonymizeStatements
{
    /**
     * Pseudonymize statements.
     *
     * @param  array  $statements
     * @return array
     */
    public function pseudonymizeStatements(array $statements): array
    {
        $pseudos = [];
        return array_map(function ($statement) use (&$pseudos) {
            return XapiAgent::pseudonymizeStatement($statement, $pseudos);
        }, $statements);
    }
}
