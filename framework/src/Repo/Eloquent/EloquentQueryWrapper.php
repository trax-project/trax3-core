<?php

namespace Trax\Framework\Repo\Eloquent;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Trax\Framework\Repo\Repository;
use Trax\Framework\Repo\HasFilters;
use Trax\Framework\Repo\Query;

class EloquentQueryWrapper
{
    use HasFilters;
    
    /**
     * DB additional specific grammar.
     *
     * @var \Trax\Framework\Repo\Eloquent\Grammar
     */
    protected $grammar;
    
    /**
     * The calling repository.
     *
     * @var \Trax\Framework\Repo\Repository
     */
    protected $repo;

    /**
     * The model associated with the builder.
     *
     * @var string
     */
    protected $model;

    /**
     * DB table when DB query builder is prefered to Eloquent.
     *
     * @var string
     */
    protected $table;

    /**
     * The filters implemented by the repository.
     *
     * @var array
     */
    protected $dynamicFilters;

    /**
     * Don't use Eloquent to get data.
     *
     * @var bool
     */
    protected $dontGetWithEloquent = false;

    /**
     * Does the query use a JOIN.
     * There is nurrently only one such case which is when a sort param is based on a relation.
     * This property is used to adapt the query in really specific limited cases.
     *
     * @var bool
     */
    protected $joined = false;

    /**
     * Query.
     *
     * @var \Trax\Framework\Repo\Query
     */
    protected $query;

    /**
     * Constructor.
     *
     * @param \Trax\Framework\Repo\Repository  $repo
     * @param string  $model
     * @param string  $table  May be specified when when want a direct request on the table, not on the Eloquent model
     * @param array  $dynamicFilters
     * @return void
     */
    public function __construct(Repository $repo, string $model, string $table = null, array $dynamicFilters = [])
    {
        $this->grammar = GrammarFactory::make($model);
        $this->repo = $repo;
        $this->model = $model;
        $this->table = $table;
        $this->dynamicFilters = $dynamicFilters;
    }

    /**
     * Skip Eloquent for get requests.
     *
     * @return void
     */
    public function dontGetWithEloquent()
    {
        $this->dontGetWithEloquent = true;
    }

    /**
     * Find an existing resource given its ID.
     *
     * @param mixed  $id
     * @param \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find($id, Query $query)
    {
        // Get results.
        $result = $this->queriedBuilder($query, true)->find($id);

        if (empty($result)) {
            return null;
        }

        // Append accessors.
        if (isset($query) && !empty($query->append())) {
            $result->append($query->append());
        }
        
        return $this->response($result);
    }

    /**
     * Find an existing resource given its ID.
     *
     * @param mixed  $id
     * @param \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id, Query $query)
    {
        $result = $this->find($id, $query);
        
        if (empty($result)) {
            throw new ModelNotFoundException('Model not found');
        }

        return $result;
    }

    /**
     * Get resources.
     *
     * @param \Trax\Framework\Repo\Query  $query
     * @return \Illuminate\Support\Collection
     */
    public function get(Query $query): Collection
    {
        $builder = $this->queriedBuilder($query);

        //Log::channel('benchmark')->info($builder->toSql());

        //dd($builder->toSql(), $builder->getBindings());

        // Get results.
        $result = $builder->get();
        
        // Append accessors.
        if (isset($query) && !empty($query->append())) {
            $result->each->append($query->append());
        }

        return $this->response($result);
    }

    /**
     * Update resources.
     *
     * @param \Trax\Framework\Repo\Query  $query
     * @param array  $data
     * @return void
     */
    public function update(Query $query, array $data): void
    {
        $builder = $this->queriedBuilder($query, true);
        $builder->update($data);
        $this->reinit();
    }

    /**
     * Delete resources.
     *
     * @param \Trax\Framework\Repo\Query  $query
     * @return void
     */
    public function delete(Query $query): void
    {
        $builder = $this->queriedBuilder($query, true);
        $builder->delete();
        $this->reinit();
    }

    /**
     * Count resources, limited to pagination when provided.
     *
     * @param \Trax\Framework\Repo\Query  $query
     * @return int
     */
    public function count(Query $query): int
    {
        $builder = $this->queriedBuilder($query);
        $count = $builder->count();
        $this->reinit();
        return $count;
    }

    /**
     * Count all resources, without pagination params.
     *
     * @param \Trax\Framework\Repo\Query  $query
     * @return int
     */
    public function countAll(Query $query): int
    {
        $builder = $this->queriedBuilder($query, true);
        $count = $builder->count();
        $this->reinit();
        return $count;
    }

    /**
     * Return the query builder with a query already built.
     *
     * @param  \Trax\Framework\Repo\Query  $query
     * @param  bool  $noLimit
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function queriedBuilder(Query $query, bool $noLimit = false)
    {
        $this->joined = false;

        // Get builder with With and/or more filters.
        $this->query = $query->addFilter($this->filters);
        $builder = $this->builder();

        // Use distinct when relevant.
        if (!empty($this->query->distinct())) {
            $builder = $builder->distinct()->select($this->query->distinct());
        }

        // Sort results.
        foreach ($query->sortInfo() as $sortInfo) {
            if (is_null($sortInfo['rel'])) {
                // Simple orderBy.
                $builder->orderBy($sortInfo['col'], $sortInfo['dir']);
            } else {
                // Order by applied on a belongTo relation.
                $this->joined = true;
                $relationName = $sortInfo['rel'];
                $foreignKey = $this->table . '.' . $relationName . '_id';
                $relation = (new $this->model)->$relationName();
                $joinedTable = $relation->getRelated()->getTable();
                $joinedTableforeignKey = $relation->getRelated()->getQualifiedKeyName();

                $builder->select("$this->table.*")
                    ->join($joinedTable, $foreignKey, '=', $joinedTableforeignKey)
                    ->orderBy($joinedTable . '.' . $sortInfo['col'], $sortInfo['dir']);
            }
        }

        // Limit and skip.
        if (!$noLimit) {
            $builder->limit($query->limit());
            $builder->skip($query->skip());
        }

        // Before.
        if ($query->hasBefore()) {
            list($col, $val) = $query->beforeInfo();
            $builder->where($col, '<', $val)->orderBy($col, 'desc');
        }

        // After.
        if ($query->hasAfter()) {
            list($col, $val) = $query->afterInfo();
            $builder->where($col, '>', $val)->orderBy($col, 'asc');
        }

        // Filter.
        $this->processFilters($builder, $query->filters());

        return $builder;
    }

    /**
     * Perform a query on a given builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $builder
     * @param  array  $filters
     * @return void
     */
    protected function processFilters($builder, array $filters, bool $or = false)
    {
        $filters = $this->serializeFilters($filters);
        $orWhere = false;
        foreach ($filters as $condition) {
            $this->addCondition($builder, $condition, $orWhere);
            $orWhere = $or;
        }
    }

    /**
     * Add a condition to the query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $builder
     * @param  array  $condition
     * @param  bool  $orWhere
     * @return void
     */
    protected function addCondition($builder, array $condition, bool $orWhere)
    {
        // We always have 1 condition, but we loop to get the $prop easily.
        foreach ($condition as $prop => $value) {
            if (in_array($prop, Query::keywords())) {
                // Logical operators.
                if ($prop == '$or') {
                    $this->addOrCondition($builder, $value, $orWhere);
                } elseif ($prop == '$and') {
                    $this->addAndCondition($builder, $value, $orWhere);
                }
            } elseif (in_array($prop, $this->dynamicFilters)) {
                // Filter.
                $this->addFilterCondition($builder, $prop, $value, $orWhere);
            } else {
                // Property.
                $this->addPropertyCondition($builder, $prop, $value, $orWhere);
            }
        }
    }

    /**
     * Add a logical OR condition to the query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $builder
     * @param  array  $conditions
     * @param  bool  $orWhere
     * @return void
     */
    protected function addOrCondition($builder, array $filter, bool $orWhere)
    {
        $where = $orWhere ? 'orWhere' : 'where';
        $builder->$where(function ($builder) use ($filter) {
            $this->processFilters($builder, $filter, true);
        });
    }

    /**
     * Add a logical AND condition to the query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $builder
     * @param  array  $conditions
     * @param  bool  $orWhere
     * @return void
     */
    protected function addAndCondition($builder, array $filter, bool $orWhere)
    {
        $where = $orWhere ? 'orWhere' : 'where';
        $builder->$where(function ($builder) use ($filter) {
            $this->processFilters($builder, $filter);
        });
    }

    /**
     * Add a filter condition to the query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $builder
     * @param  string  $filterProp
     * @param  mixed  $filterValue
     * @param  bool  $orWhere
     * @return void
     */
    protected function addFilterCondition($builder, string $filterProp, $filterValue, bool $orWhere)
    {
        $where = $orWhere ? 'orWhere' : 'where';
        $builder->$where(function ($builder) use ($filterProp, $filterValue) {
            $method = Str::camel($filterProp) . 'Filter';
            $filter = $this->repo->$method($filterValue, $this->query);
            $this->processFilters($builder, $filter);
        });
    }

    /**
     * Add a property condition to the query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $builder
     * @param  string  $prop
     * @param  mixed  $condition
     * @param  bool  $orWhere
     * @return mixed
     */
    protected function addPropertyCondition($builder, string $prop, $condition, bool $orWhere)
    {
        $where = $orWhere ? 'orWhere' : 'where';

        // Unit tests replace * by __asterisk__
        $prop = str_replace('__asterisk__', '*', $prop);
        $prop = $this->joined ? $this->table.'.'.$prop : $prop;

        // Scalar value (string, integer, etc.) or JSON value.
        // JSON values must be in an array form, with more than 1 property!
        if (!is_array($condition) || count($condition) > 1) {
            $value = $condition;
            if (strpos($prop, '[*]') !== false || is_array($value)) {
                return $this->grammar->addJsonContainsCondition($builder, $prop, $value, $orWhere);
            } else {
                return $builder->$where($prop, $value);
            }
        }
        // Get operator and value.
        foreach ($condition as $operator => $value) {
            break;
        }

        // JSON search.
        if (strpos($prop, '[*]') !== false && $operator == '$text') {
            return $this->grammar->addJsonSearchCondition($builder, $prop, $value, $orWhere);
        }

        // Other operators.
        switch ($operator) {
            case '$eq':
                return $builder->$where($prop, $value);
            case '$gt':
                return $builder->$where($prop, '>', $value);
            case '$gte':
                return $builder->$where($prop, '>=', $value);
            case '$lt':
                return $builder->$where($prop, '<', $value);
            case '$lte':
                return $builder->$where($prop, '<=', $value);
            case '$ne':
                return $this->grammar->notEqualCondition($builder, $prop, $value, $orWhere);
            case '$in':
                return $builder->{$where.'In'}($prop, $value);
            case '$nin':
                return $this->grammar->notInCondition($builder, $prop, $value, $orWhere);
            case '$text':
                return $this->grammar->likeCondition($builder, $prop, $value, $orWhere);
            case '$exists':
                if ($value) {
                    return $builder->{$where.'NotNull'}($prop);
                } else {
                    return $builder->{$where.'Null'}($prop);
                }
            case '$has':
                // Be sure to skip the table name before passing the relation to the whereHas function.
                $prop = (string) Str::of($prop)->afterLast('.');
                return $builder->{$where.'Has'}($prop, $value);
        }
    }

    /**
     * Return the query builder.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function builder()
    {
        // Get the DB query builder when it is prefered to Eloquent.
        if ($this->dontGetWithEloquent) {
            return DB::table($this->table);
        }
        // ELoquent query builder.
        return $this->eloquentBuilder();
    }

    /**
     * Return the Eloquent query builder.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function eloquentBuilder()
    {
        $queryBuilder = $this->model::query();
        if (!is_null($this->query)) {
            $queryBuilder = $queryBuilder->with($this->query->with());
        }
        return $queryBuilder;
    }

    /**
     * Finalize the response.
     *
     * @param  \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection  $result
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection
     */
    protected function response($result)
    {
        if (is_null($result)) {
            $response = null;
        } elseif ($result instanceof Collection) {
            $response = $this->repo->finalize($result, $this->query);
        } else {
            $response = $this->repo->finalize(collect([$result]), $this->query)->first();
        }
        $this->reinit();
        return $response;
    }

    /**
     * Reinitialize.
     *
     * @return void
     */
    protected function reinit()
    {
        $this->clearFilters();
        $this->query = null;
    }
}
