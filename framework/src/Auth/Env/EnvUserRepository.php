<?php

namespace Trax\Framework\Auth\Env;

use Trax\Framework\Auth\UserRepository;
use Illuminate\Support\Collection;
use Trax\Framework\Repo\Query;

class EnvUserRepository implements UserRepository
{
    /**
     * Create a user.
     *
     * @param  array  $data
     * @return \Trax\Framework\Auth\User|null
     */
    public function create(array $data)
    {
        return new EnvUser;
    }

    /**
     * Return all the users.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(): Collection
    {
        return collect([
            new EnvUser
        ]);
    }

    /**
     * Return a user given its ID.
     *
     * @param  mixed  $id
     * @return \Trax\Framework\Auth\User|null
     */
    public function find($id)
    {
        if ($id != 1) {
            return null;
        }
        return new EnvUser;
    }

    /**
     * Return a user given its email.
     *
     * @param  string  $email
     * @return \Trax\Framework\Auth\User|null
     */
    public function findByEmail(string $email)
    {
        if ($email != config('trax.auth.admin.email')) {
            return null;
        }
        return new EnvUser;
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
