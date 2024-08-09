<?php

namespace Trax\Activities\Repos\Activity\Actions;

use Trax\Framework\Repo\Query;
use Trax\Framework\Xapi\Helpers\XapiActivity;

trait OneFunctions
{
    /**
     * Get an existing resource given its params.
     *
     * @param  string  $iri
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getOne(string $iri)
    {
        return $this->findFromQuery(
            $this->oneQuery($iri)
        );
    }

    /**
     * Delete an existing resource given its params.
     *
     * @param  string  $iri
     * @return void
     */
    public function deleteOne(string $iri)
    {
        $this->deleteByQuery(
            $this->oneQuery($iri)
        );
    }

    /**
     * Get the query to target one resource given its params.
     *
     * @param  string  $iri
     * @return \Trax\Framework\Repo\Query
     */
    protected function oneQuery(string $iri): Query
    {
        return new Query(['filters' => [
            'id' => XapiActivity::hashId($iri),
        ]]);
    }
}
