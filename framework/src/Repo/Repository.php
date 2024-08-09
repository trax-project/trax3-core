<?php

namespace Trax\Framework\Repo;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Trax\Framework\Repo\Query;
use Trax\Framework\Repo\Actions\FinalizeTimestamps;
use Trax\Framework\Xapi\Helpers\Json;
use Trax\Framework\Auth\Authorizer;
use Trax\Framework\Context;

abstract class Repository implements RepositoryInterface
{
    use FinalizeTimestamps;
    
    /**
     * The applicable domain.
     */
    protected $domain = 'TO BE DEFINED';

    /**
     * The applicable scopes.
     */
    protected $scopes = [];

    /**
     * @var \Trax\Framework\Repo\ModelFactory
     */
    protected $factory;

    /**
     * @var bool
     */
    protected $mineScopeEnabled = true;

    /**
     * @var bool
     */
    protected $jsonEncode = true;

    /**
     * Return the domain.
     *
     * @return string
     */
    public function domain(): string
    {
        return $this->domain;
    }

    /**
     * Return the scopes.
     *
     * @return array
     */
    public function scopes(): array
    {
        return $this->scopes;
    }

    /**
     * Return the class of the factory.
     *
     * @return string
     */
    abstract public function factoryClass();

    /**
     * Return the model factory.
     *
     * @return \Trax\Framework\Repo\ModelFactory
     */
    public function factory()
    {
        if (is_null($this->factory)) {
            $factoryClass = $this->factoryClass();
            $this->factory = new $factoryClass($this->jsonEncode);
        }
        return $this->factory;
    }

    /**
     * Return the connection name.
     *
     * @return string
     */
    public function connection(): string
    {
        return $this->factory()->connection();
    }

    /**
     * Return the model table.
     *
     * @return string
     */
    public function table(): string
    {
        return $this->factory()->table();
    }

    /**
     * Disable the mine scope.
     *
     * @return \Trax\Framework\Repo\RepositoryInterface
     */
    public function disableMineScope(): RepositoryInterface
    {
        $this->mineScopeEnabled = false;
        return $this;
    }

    /**
     * Return the auth filters.
     *
     * @return array
     */
    public function scopingFilters(): array
    {
        // We always filter on store.
        $filters = app(Authorizer::class)->storeScopeFilters($this);

        // Mine scope must be enabled.
        if ($this->mineScopeEnabled) {
            $filters = array_merge(
                $filters,
                app(Authorizer::class)->mineScopeFilters($this)
            );
        }

        // We restore the mine scope after each request.
        $this->mineScopeEnabled = true;

        return $filters;
    }

    /**
     * Return the user mine filters.
     *
     * @return array
     */
    public function mineUserFilters(): array
    {
        return [];
    }

    /**
     * Return the client mine filters.
     *
     * @return array
     */
    public function mineClientFilters(): array
    {
        return [
            ['client' => Context::client()]
        ];
    }

    /**
     * Get the dynamic filters.
     *
     * @return array
     */
    public function dynamicFilters(): array
    {
        return [];
    }

    /**
     * Check if we can communicate with the database.
     *
     * @return object  (object)['ready' => false, 'reason' => 'Authentication failed']
     */
    public function checkStatus()
    {
        try {
            $this->get(new Query(['limit' => 1]));
            return (object)['ready' => true, 'reason' => ''];
        } catch (\Exception $e) {
            return (object)['ready' => false, 'reason' => $e->getMessage()];
        }
    }

    /**
     * Add a filter.
     *
     * @param  array  $filter
     * @return \Trax\Framework\Repo\Repository
     */
    abstract public function addFilter(array $filter = []);

    /**
     * Remove filters and return them.
     *
     * @return array
     */
    abstract public function removeFilters(): array;

    /**
     * Create a new resource.
     *
     * @param  array  $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        $inserted = $this->insert([$data])[0];
        return $this->factory()->model()->fill($inserted);
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

        $this->updateByQuery(new Query([
            'filters' => ['id' => $model->id]
        ]), $model->toArray());

        return $model;
    }

    /**
     * Update existing resources, given their IDs.
     *
     * @param  array  $ids
     * @param  array  $data
     * @return void
     */
    public function updateByIds(array $ids, array $data = null)
    {
        $this->updateByQuery(new Query([
            'filters' => ['id' => ['$in' => $ids]]
        ]), $data);
    }

    /**
     * Update existing resources, given a query.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @param  array  $data
     * @return void
     */
    abstract public function updateByQuery(Query $query, array $data);

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
        $this->create($copy->toArray());
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
    abstract public function insert(array $batch, $prepareFunction = 'prepare'): array;

    /**
     * Upsert a batch of resource.
     * Returns the upserted data.
     *
     * @param  array  $batch
     * @param  string  $prepareFunction
     * @return array
     */
    abstract public function upsert(array $batch, $prepareFunction = 'prepare'): array;

    /**
     * Prepare a batch of resource before insert or upsert.
     *
     * @param  array  $batch
     * @param  string  $prepareFunction
     * @return array
     */
    protected function prepareBatch(array $batch, $prepareFunction = 'prepare'): array
    {
        $batch = array_filter(array_map(function ($data) use ($prepareFunction) {
            // Be sure to work on arrays.
            return $this->factory()->{$prepareFunction}(Json::array($data));
        }, $batch));

        return $this->uniqueInBatch($batch);
    }

    /**
     * Be sure to remove duplicates in the batch.
     *
     * @param  array  $batch
     * @return array
     */
    protected function uniqueInBatch(array $batch): array
    {
        return collect($batch)->unique(function ($record) {
            return $this->factory()->uniqueColumnsValue($record);
        })->values()->all();
    }

    /**
     * Delete an existing resource.
     *
     * @param  mixed  $id
     * @return void
     */
    public function delete($id)
    {
        $this->deleteByQuery(new Query([
            'filters' => ['id' => $id]
        ]));
    }

    /**
     * Delete an existing resource, given its model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function deleteModel($model)
    {
        $this->deleteByQuery(new Query([
            'filters' => ['id' => $model->id]
        ]));
    }

    /**
     * Delete existing resources, given their models.
     *
     * @param  \Illuminate\Support\Collection  $models
     * @return void
     */
    public function deleteModels($models)
    {
        $this->deleteByQuery(new Query([
            'filters' => ['id' => ['$in' => $models->pluck('id')]]
        ]));
    }

    /**
     * Clear the repo data, taking into account the store.
     *
     * @return void
     */
    public function clear()
    {
        $this->deleteByQuery(new Query);
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
        $resource = $this->addFilter(
            ['id' => $id]
        )->get($query)->first();

        return $this->model($resource);
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
        if (!$resource = $this->find($id, $query)) {
            throw new ModelNotFoundException($this->factory()->modelName() . ' model with ID ' . $id . ' not found.');
        }
        return $resource;
    }

    /**
     * Find an existing resource given a query.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findFromQuery(Query $query = null)
    {
        return $this->model(
            $this->get($query)->first()
        );
    }

    /**
     * Find existing resources given a list of IDs.
     * The returned value is a collection of Eloquent models or objects.
     *
     * @param  array|\Illuminate\Support\Collection  $ids
     * @return \Illuminate\Support\Collection
     */
    public function in($ids): Collection
    {
        return $this->addFilter(
            ['id' => ['$in' => $ids]]
        )->get();
    }

    /**
     * Get all resources.
     * The returned value is a collection of Eloquent models or objects.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all(): Collection
    {
        return $this->get();
    }

    /**
     * Get resources given a set of filters.
     * The returned value is a collection of Eloquent models or objects.
     *
     * @param  array  $filters
     * @return \Illuminate\Support\Collection
     */
    public function filter(array $filters = []): Collection
    {
        return $this->addFilter($filters)->get();
    }

    /**
     * Get the first resource matching with a query, or return null.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function first(Query $query = null)
    {
        return $this->get($query)->first();
    }

    /**
     * Get resources.
     * The returned value is a collection of Eloquent models or objects.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Support\Collection
     */
    abstract public function get(Query $query = null): Collection;

    /**
     * Count resources, limited to pagination when provided.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return int
     */
    abstract public function count(Query $query = null): int;

    /**
     * Count all resources, without pagination params.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @return int
     */
    abstract public function countAll(Query $query = null): int;

    /**
     * Get the resource after.
     *
     * @param  mixed  $value
     * @param  string  $column
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function after($value, string $column = 'id')
    {
        $resources = $this->get(new Query([
            'after' => [$column => $value],
            'limit' => 1,
        ]));

        return $this->model(
            $resources->count() == 1 ? $resources->last() : null
        );
    }

    /**
     * Get the resource before.
     *
     * @param  mixed  $value
     * @param  string  $column
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function before($value, string $column = 'id')
    {
        $resources = $this->get(new Query([
            'before' => [$column => $value],
            'limit' => 1,
        ]));

        return $this->model(
            $resources->count() == 1 ? $resources->last() : null
        );
    }

    /**
     * Finalize the resources before returning them.
     *
     * @param  \Illuminate\Support\Collection  $resources
     * @param  \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Support\Collection
     */
    public function finalize(Collection $resources, Query $query = null): Collection
    {
        return $this->finalizeTimestamps($resources);
    }

    /**
     * Convert a resource into an Eloquent model.
     *
     * @param  \Illuminate\Database\Eloquent\Model|object|null  $resource
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function model($resource = null)
    {
        if (is_null($resource)) {
            return null;
        }
        if ($resource instanceof Model) {
            return $resource;
        }
        if (is_array($resource)) {
            return $this->factory()->model()->fill($resource);
        }
        if (is_object($resource)) {
            return $this->factory()->model()->fill((array)$resource);
        }
        return null;
    }
}
