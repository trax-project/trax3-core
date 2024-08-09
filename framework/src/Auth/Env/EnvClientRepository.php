<?php

namespace Trax\Framework\Auth\Env;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Trax\Framework\Auth\ClientRepository;
use Trax\Framework\Repo\Query;

class EnvClientRepository implements ClientRepository
{
    /**
     * Create a client.
     *
     * @param  array  $data
     * @return \Trax\Framework\Auth\Client|null
     */
    public function create(array $data)
    {
        return new EnvClient;
    }

    /**
     * Return all the clients.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(): Collection
    {
        return collect([
            new EnvClient
        ]);
    }

    /**
     * Return a client given its ID.
     *
     * @param  mixed  $id
     * @return \Trax\Framework\Auth\Client|null
     */
    public function find($id)
    {
        if ($id != 1) {
            return null;
        }
        return new EnvClient;
    }

    /**
     * Return a client given its slug.
     *
     * @param  string  $slug
     * @return \Trax\Framework\Auth\Client|null
     */
    public function findBySlug(string $slug)
    {
        if ($slug !== 'default') {
            return null;
        }
        return new EnvClient;
    }
        
    /**
     * Return a client given its slug, or throw an exception.
     *
     * @param  string  $slug
     * @return \Trax\Framework\Auth\Client
     *
     * @throw \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findBySlugOrFail(string $slug)
    {
        if (!$resource = $this->findBySlug($slug)) {
            throw new ModelNotFoundException('client model with slug ' . $slug . ' not found.');
        }
        return $resource;
    }

    /**
     * Delete existing resources, given a query.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return void
     */
    public function deleteByQuery(Query $query)
    {
        // This repo is not smart enought to parse the query :)
    }
}
