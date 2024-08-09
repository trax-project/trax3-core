<?php

namespace Trax\Framework\Auth;

use Illuminate\Support\Collection;
use Trax\Framework\Repo\Query;

interface StoreRepository
{
    /**
     * Create a store.
     *
     * @param  array  $data
     * @return \Trax\Framework\Auth\Store|null
     */
    public function create(array $data);

    /**
     * Return all the clients.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(): Collection;

    /**
     * Get ready stores.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getReady(): Collection;

    /**
     * Return a store given its ID.
     *
     * @param  mixed  $id
     * @return \Trax\Framework\Auth\Store|null
     */
    public function find($id);

    /**
     * Return a store given its slug.
     *
     * @param  string  $slug
     * @return \Trax\Framework\Auth\Store|null
     */
    public function findBySlug(string $slug);

    
    /**
     * Return a client store its slug, or throw an exception.
     *
     * @param  string  $slug
     * @return \Trax\Framework\Auth\Store
     *
     * @throw \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findBySlugOrFail(string $slug);
    
    /**
     * Delete an existing resource given its ID.
     *
     * @param  mixed  $id
     * @return void
     */
    public function delete($id);

    /**
     * Delete an existing resource given its slug.
     *
     * @param  string  $slug
     * @return void
     */
    public function deleteBySlug(string $slug);

    /**
     * Delete existing resources, given a query.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return void
     */
    public function deleteByQuery(Query $query);
}
