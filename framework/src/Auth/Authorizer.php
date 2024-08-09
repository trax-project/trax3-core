<?php

namespace Trax\Framework\Auth;

use Trax\Framework\Repo\RepositoryInterface;

interface Authorizer
{
    /**
     * Check that the store request is authorized on the current consumer.
     *
     * @param  string  $store
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function checkConsumerStore(string $store);

    /**
     * Check a capability and throw an exception when the capability is not granted.
     *
     * @param string  $capability  domain.operation(.scope)
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function must(string $capability): void;

    /**
     * Return the filters to be applied on a repository for the mine scope.
     *
     * @param  \Trax\Framework\Repo\RepositoryInterface  $repo
     * @return array
     */
    public function mineScopeFilters(RepositoryInterface $repo): array;

    /**
     * Return the filters to be applied on a repository for the store scope.
     *
     * @param  \Trax\Framework\Repo\RepositoryInterface  $repo
     * @return array
     */
    public function storeScopeFilters(RepositoryInterface $repo): array;
}
