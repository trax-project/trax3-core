<?php

namespace Trax\Framework\Repo;

use Illuminate\Support\Collection;
use Trax\Framework\Repo\Query;

interface RepositoryInterface
{
    /**
     * Return the domain.
     *
     * @return string
     */
    public function domain(): string;

    /**
     * Return the scopes.
     *
     * @return array
     */
    public function scopes(): array;
    
    /**
     * Return the class of the factory.
     *
     * @return string
     */
    public function factoryClass();

    /**
     * Return the model factory.
     *
     * @return \Trax\Framework\Repo\ModelFactory
     */
    public function factory();

    /**
     * Return the connection name.
     *
     * @return string
     */
    public function connection(): string;

    /**
     * Return the model table.
     *
     * @return string
     */
    public function table(): string;

    /**
     * Return the auth filters.
     *
     * @return array
     */
    public function scopingFilters(): array;

    /**
     * Return the user mine filters.
     *
     * @return array
     */
    public function mineUserFilters(): array;

    /**
     * Return the client mine filters.
     *
     * @return array
     */
    public function mineClientFilters(): array;

    /**
     * Get the dynamic filters.
     *
     * @return array
     */
    public function dynamicFilters(): array;

    /**
     * Check if we can communicate with the database.
     *
     * @return object  (object)['ready' => false, 'reason' => 'Authentication failed']
     */
    public function checkStatus();

    /**
     * Add a filter.
     *
     * @param  array  $filter
     * @return \Trax\Framework\Repo\Repository
     */
    public function addFilter(array $filter = []);

    /**
     * Remove filters and return them.
     *
     * @return array
     */
    public function removeFilters(): array;

    /**
     * Create a new resource.
     *
     * @param  array  $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data);

    /**
     * Update an existing resource, given its ID.
     *
     * @param  mixed  $id
     * @param  array  $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($id, array $data);

    /**
     * Update an existing resource, given its model and new data.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $data
     * @param  mixed  $options
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateModel($model, array $data = null, $options = null);

    /**
     * Update existing resources, given their IDs.
     *
     * @param  array  $ids
     * @param  array  $data
     * @return void
     */
    public function updateByIds(array $ids, array $data = null);

    /**
     * Update existing resources, given a query.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @param  array  $data
     * @return void
     */
    public function updateByQuery(Query $query, array $data);

    /**
     * Duplicate an existing resource, given its model and new data.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function duplicateModel($model, array $data = null);

    /**
     * Insert a batch of resource.
     * Returns the inserted data.
     *
     * @param  array  $batch
     * @param  string  $prepareFunction
     * @return array
     */
    public function insert(array $batch, $prepareFunction = 'prepare'): array;

    /**
     * Upsert a batch of resource.
     * Returns the upserted data.
     *
     * @param  array  $batch
     * @param  string  $prepareFunction
     * @return array
     */
    public function upsert(array $batch, $prepareFunction = 'prepare'): array;

    /**
     * Delete an existing resource.
     *
     * @param  mixed  $id
     * @return void
     */
    public function delete($id);

    /**
     * Delete an existing resource, given its model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function deleteModel($model);

    /**
     * Delete existing resources, given their models.
     *
     * @param  \Illuminate\Support\Collection  $models
     * @return void
     */
    public function deleteModels($models);

    /**
     * Delete existing resources, given a query.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return void
     */
    public function deleteByQuery(Query $query);

    /**
     * Clear the repo data, taking into account the store.
     *
     * @return void
     */
    public function clear();

    /**
     * Find an existing resource given its ID.
     *
     * @param  mixed  $id
     * @param  \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find($id, Query $query = null);

    /**
     * Find an existing resource given its ID.
     *
     * @param  mixed  $id
     * @param  \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id, Query $query = null);

    /**
     * Find an existing resource given a query.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findFromQuery(Query $query = null);

    /**
     * Find existing resources given a list of IDs.
     * The returned value is a collection of Eloquent models or objects.
     *
     * @param  array|\Illuminate\Support\Collection  $ids
     * @return \Illuminate\Support\Collection
     */
    public function in($ids): Collection;

    /**
     * Get all resources.
     * The returned value is a collection of Eloquent models or objects.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all(): Collection;

    /**
     * Get resources given a set of filters.
     * The returned value is a collection of Eloquent models or objects.
     *
     * @param  array  $filters
     * @return \Illuminate\Support\Collection
     */
    public function filter(array $filters = []): Collection;

    /**
     * Get the first resource matching with a query, or return null.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function first(Query $query = null);

    /**
     * Get resources.
     * The returned value is a collection of Eloquent models or objects.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Support\Collection
     */
    public function get(Query $query = null): Collection;

    /**
     * Count resources, limited to pagination when provided.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return int
     */
    public function count(Query $query = null): int;

    /**
     * Count all resources, without pagination params.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return int
     */
    public function countAll(Query $query = null): int;

    /**
     * Get the resource after.
     *
     * @param  mixed  $value
     * @param  string  $column
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function after($value, string $column = 'id');

    /**
     * Get the resource before.
     *
     * @param  mixed  $value
     * @param  string  $column
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function before($value, string $column = 'id');

    /**
     * Finalize the resources before returning them.
     *
     * @param  \Illuminate\Support\Collection  $resources
     * @param  \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Support\Collection
     */
    public function finalize(Collection $resources, Query $query = null): Collection;
}
