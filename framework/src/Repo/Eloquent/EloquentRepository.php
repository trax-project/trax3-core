<?php

namespace Trax\Framework\Repo\Eloquent;

use Illuminate\Support\Collection;
use Trax\Framework\Repo\Repository;
use Trax\Framework\Repo\Query;

abstract class EloquentRepository extends Repository
{
    /**
     * Query builder.
     *
     * @var \Trax\Framework\Repo\Eloquent\EloquentQueryWrapper
     */
    protected $builder;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->builder = new EloquentQueryWrapper(
            $this,
            $this->factory()->modelClass(),
            $this->factory()->table(),
            $this->dynamicFilters()
        );
    }

    /**
     * Skip Eloquent for get requests.
     *
     * @return void
     */
    public function dontGetWithEloquent()
    {
        $this->builder->dontGetWithEloquent();
    }

    /**
     * Add a filter.
     *
     * @param  array  $filter
     * @return \Trax\Framework\Repo\Repository
     */
    public function addFilter(array $filter = [])
    {
        $this->builder->addFilter($filter);
        return $this;
    }

    /**
     * Remove filters and return them.
     *
     * @return array
     */
    public function removeFilters(): array
    {
        return $this->builder->removeFilters();
    }

    /**
     * Create a new resource.
     *
     * @param  array  $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        $resource = $this->factory()->make($data);
        $resource->save();
        return $resource;
    }

    /**
     * Update an existing resource, given its ID.
     *
     * @param  mixed  $id
     * @param  array  $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($id, array $data)
    {
        $resource = $this->findOrFail($id);
        return $this->updateModel($resource, $data);
    }

    /**
     * Update an existing resource, given its model and new data.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $data
     * @param  mixed  $options
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateModel($model, array $data = null, $options = null)
    {
        if (isset($data)) {
            $model = $this->factory()->update($model, $data, $options);
        }
        $model->save();
        return $model;
    }

    /**
     * Update existing resources, given a query.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @param  array  $data
     * @return void
     */
    public function updateByQuery(Query $query, array $data)
    {
        // Apply security.
        $query->addFilter($this->scopingFilters());

        // Perform the request.
        $this->builder->update($query, $data);
    }

    /**
     * Duplicate an existing resource, given its model and new data.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function duplicateModel($model, array $data = null)
    {
        $copy = $this->factory()->duplicate($model, $data);
        $copy->save();
        return $copy;
    }

    /**
     * Insert a batch of resource.
     * Returns the inserted data.
     *
     * @param  array  $batch
     * @param  string  $prepareFunction
     * @return array
     */
    public function insert(array $batch, $prepareFunction = 'prepare'): array
    {
        if (empty($batch)) {
            return [];
        }
        $preparedBatch = $this->prepareBatch($batch, $prepareFunction);
        $this->factory()->modelClass()::insert($preparedBatch);
        return $preparedBatch;
    }

    /**
     * Upsert a batch of resource.
     * Returns the upserted data.
     *
     * @param  array  $batch
     * @param  string  $prepareFunction
     * @return array
     */
    public function upsert(array $batch, $prepareFunction = 'prepare'): array
    {
        if (empty($batch)) {
            return [];
        }
        $preparedBatch = $this->prepareBatch($batch, $prepareFunction);

        $this->factory()->modelClass()::upsert(
            $preparedBatch,
            $this->factory()->uniqueColumns()
        );

        return $preparedBatch;
    }

    /**
     * Delete an existing resource.
     *
     * @param  mixed  $id
     * @return void
     */
    public function delete($id)
    {
        $resource = $this->findOrFail($id);
        $this->deleteModel($resource);
    }

    /**
     * Delete an existing resource, given its model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function deleteModel($model)
    {
        $model->delete();
    }

    /**
     * Delete existing resources, given their models.
     *
     * @param  \Illuminate\Support\Collection  $models
     * @return void
     */
    public function deleteModels($models)
    {
        $this->factory()->modelClass()::destroy(
            $models->pluck('id')->toArray()
        );
    }

    /**
     * Delete existing resources, given a query.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return void
     */
    public function deleteByQuery(Query $query)
    {
        // Apply security.
        $query->addFilter($this->scopingFilters());

        // Perform the request.
        $this->builder->delete($query);
    }

    /**
     * Find an existing resource given its ID.
     *
     * @param  mixed  $id
     * @param  \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find($id, Query $query = null)
    {
        // Apply security.
        if (!isset($query)) {
            $query = new Query;
        }
        $query->addFilter($this->scopingFilters());

        // Perform the request.
        return $this->builder->find($id, $query);
    }

    /**
     * Find an existing resource given its ID.
     *
     * @param  mixed  $id
     * @param  \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id, Query $query = null)
    {
        // Apply security.
        if (!isset($query)) {
            $query = new Query;
        }
        $query->addFilter($this->scopingFilters());

        // Perform the request.
        return $this->builder->findOrFail($id, $query);
    }

    /**
     * Get resources.
     * The returned value is a collection of Eloquent models or objects.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Support\Collection
     */
    public function get(Query $query = null): Collection
    {
        // Apply security.
        if (!isset($query)) {
            $query = new Query;
        }
        $query->addFilter($this->scopingFilters());

        // Perform the request.
        return $this->builder->get($query);
    }

    /**
     * Count resources, limited to pagination when provided.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return int
     */
    public function count(Query $query = null): int
    {
        // Apply security.
        if (!isset($query)) {
            $query = new Query;
        }
        $query->addFilter($this->scopingFilters());

        // Perform the request.
        return $this->builder->count($query);
    }

    /**
     * Count all resources, without pagination params.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return int
     */
    public function countAll(Query $query = null): int
    {
        // Apply security.
        if (!isset($query)) {
            $query = new Query;
        }
        $query->addFilter($this->scopingFilters());

        // Perform the request.
        return $this->builder->countAll($query);
    }
}
