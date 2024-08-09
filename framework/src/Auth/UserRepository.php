<?php

namespace Trax\Framework\Auth;

use Illuminate\Support\Collection;
use Trax\Framework\Repo\Query;

interface UserRepository
{
    /**
     * Create a user.
     *
     * @param  array  $data
     * @return \Trax\Framework\Auth\User|null
     */
    public function create(array $data);

    /**
     * Return all the users.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(): Collection;

    /**
     * Return a user given its ID.
     *
     * @param  mixed  $id
     * @return \Trax\Framework\Auth\User|null
     */
    public function find($id);

    /**
     * Return a user given its email.
     *
     * @param  string  $email
     * @return \Trax\Framework\Auth\User|null
     */
    public function findByEmail(string $email);

    /**
     * Delete existing resources, given a query.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return void
     */
    public function deleteByQuery(Query $query);
}
