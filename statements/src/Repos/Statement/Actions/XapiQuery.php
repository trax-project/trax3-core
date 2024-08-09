<?php

namespace Trax\Statements\Repos\Statement\Actions;

use Illuminate\Support\Collection;
use Trax\Framework\Context;
use Trax\Framework\Repo\Query;

trait XapiQuery
{
    /**
     * Get a collection of Statement records given a query, following the xAPI standard process.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Support\Collection
     */
    public function xapiQuery(Query $query): Collection
    {
        $pipeline = Context::pipeline();

        // Sequence based request.
        // Get only unvoided statements.
        // Do not check targeted statements.
        if ($query->hasLimit() || (
            !$query->hasFilter('agent')
            && !$query->hasFilter('verb')
            && !$query->hasFilter('activity')
            && !$query->hasFilter('registration')
        )) {
            // Force the limit.
            if (!$query->hasLimit()) {
                $query->setLimit(config('trax.xapi.statements_limit'));
            }
            // Request.
            return $this->get(
                $query->addFilter(['voided' => false])
            );
        }

        // Not sequence based. We must check the targeted statements, including voided ones.
        // This process is not perfect because it will happen under the default limit.
        // So the number of returned statements may be under the default limit,
        // which does not mean that there is no other matching statement.
        // We recommended to limit the use of StatementRefs.

        // Force the limit.
        if (!$query->hasLimit()) {
            $query->setLimit(config('trax.xapi.statements_limit'));
        }
        // Request.
        $all = $this->get($query);
        $result = $all->where('voided', false);

        // Get targeting statements and add them to the result.
        if ($pipeline->query_targeting) {
            $targeting = $all;
            while (!$targeting->isEmpty()) {
                $targeting = $this->get(new Query(['filters' => [
                    'statement_ref' => ['$in' => $targeting->pluck('id')],
                ]]));
                $result = $result->concat($targeting);
            }
        }
        
        // Keep unvoided and unique statements.
        return $result->where('voided', false)->unique('id');
    }
}
