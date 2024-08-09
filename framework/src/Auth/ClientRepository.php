<?php

namespace Trax\Framework\Auth;

use Illuminate\Support\Collection;
use Trax\Framework\Repo\Query;

interface ClientRepository
{
    /**
     * Create a client.
     *
     * @param  array  $data
     * @return \Trax\Framework\Auth\Client|null
     */
    public function create(array $data);

    /**
     * Return all the clients.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(): Collection;

    /**
     * Return a client given its ID.
     *
     * @param  mixed  $id
     * @return \Trax\Framework\Auth\Client|null
     */
    public function find($id);

    /**
     * Return a client given its slug.
     *
     * @param  string  $slug
     * @return \Trax\Framework\Auth\Client|null
     */
    public function findBySlug(string $slug);

    /**
     * Return a client given its slug, or throw an exception.
     *
     * @param  string  $slug
     * @return \Trax\Framework\Auth\Client
     *
     * @throw \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findBySlugOrFail(string $slug);

    /**
     * Delete existing resources, given a query.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return void
     */
    public function deleteByQuery(Query $query);
}
