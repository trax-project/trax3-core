<?php

namespace Trax\Framework\Auth\Env;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Trax\Framework\Auth\StoreRepository;
use Trax\Framework\Repo\Query;

class EnvStoreRepository implements StoreRepository
{
    /**
     * Create a store.
     *
     * @param  array  $data
     * @return \Trax\Framework\Auth\Store|null
     */
    public function create(array $data)
    {
        return new EnvStore;
    }

    /**
     * Return all the clients.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(): Collection
    {
        return collect([
            new EnvStore
        ]);
    }

    /**
     * Get ready stores.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getReady(): Collection
    {
        return $this->get();
    }

    /**
     * Return a store given its ID.
     *
     * @param  mixed  $id
     * @return \Trax\Framework\Auth\Store|null
     */
    public function find($id)
    {
        if ($id != 1) {
            return null;
        }
        return new EnvStore;
    }

    /**
     * Return a store given its slug.
     *
     * @param  string  $slug
     * @return \Trax\Framework\Auth\Store|null
     */
    public function findBySlug(string $slug)
    {
        if ($slug !== 'default') {
            return null;
        }
        return new EnvStore;
    }
        
    /**
     * Return a client store its slug, or throw an exception.
     *
     * @param  string  $slug
     * @return \Trax\Framework\Auth\Store
     *
     * @throw \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findBySlugOrFail(string $slug)
    {
        if (!$resource = $this->findBySlug($slug)) {
            throw new ModelNotFoundException('store model with slug ' . $slug . ' not found.');
        }
        return $resource;
    }

    /**
     * Delete an existing resource given its ID.
     *
     * @param  mixed  $id
     * @return void
     */
    public function delete($id)
    {
        // This repo is not smart enought to do that :)
    }

    /**
     * Delete an existing resource given its slug.
     *
     * @param  string  $slug
     * @return void
     */
    public function deleteBySlug(string $slug)
    {
        // This repo is not smart enought to do that :)
    }

    /**
     * Delete existing resources, given a query.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return void
     */
    public function deleteByQuery(Query $query)
    {
        // This repo is not smart enought to do that :)
    }
}
