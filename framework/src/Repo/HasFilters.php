<?php

namespace Trax\Framework\Repo;

trait HasFilters
{
    /**
     * Filter.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Has a filter?
     *
     * @param  string  $name
     * @return bool
     */
    public function hasFilter(string $name): bool
    {
        return !is_null($this->filter($name));
    }

    /**
     * Get filters.
     *
     * @return array
     */
    public function filters(): array
    {
        // Always serialize the filters before returning them.
        $this->filters = $this->serializeFilters($this->filters);

        return $this->filters;
    }

    /**
     * Get a given filter.
     *
     * @param  string  $name
     * @return mixed
     */
    public function filter(string $name)
    {
        // Always serialize the filters before parsing them.
        $this->filters = $this->serializeFilters($this->filters);

        foreach ($this->filters as $filter) {
            foreach ($filter as $prop => $value) {
                if ($prop == $name) {
                    return $value;
                }
            }
        }
        return null;
    }

    /**
     * Add a filter.
     *
     * @param array  $filter
     * @return \Trax\Framework\Repo\Query
     */
    public function addFilter(array $filter = [])
    {
        $filters = $this->serializeFilters($filter);
        $this->filters = array_merge($this->filters(), $filters);
        return $this;
    }

    /**
     * Remove filters and return them.
     *
     * @return array
     */
    public function removeFilters(): array
    {
        $response = $this->filters();
        $this->clearFilters();
        return $response;
    }

    /**
     * Remove a given filter.
     *
     * @param  string  $name
     * @return void
     */
    public function removeFilter(string $name)
    {
        // Always serialize the filters before parsing them.
        $this->filters = $this->serializeFilters($this->filters);

        foreach ($this->filters as $index => &$filter) {
            foreach ($filter as $prop => $value) {
                if ($prop == $name) {
                    unset($filter[$name]);
                    if (empty($filter)) {
                        unset($this->filters[$index]);
                    }
                    return;
                }
            }
        }
    }

    /**
     * Serialize filters.
     *
     * At the end, each filter must be an array: [prop1 => value1], [prop2 => value2]
     * so we can merge several lists of filters without conflict: [prop => [$gt => 10]], [prop => [$lt => 30]]
     *
     * However, we want to allow simpler forms: [prop1 => value1, prop2 => value2]
     * So we must transform then into [[prop1 => value1], [prop2 => value2]] to avoid conflicts.
     *
     * @param  array  $filter
     * @return array
     */
    protected function serializeFilters(array $filters): array
    {
        // We may have an associative array at the first level, or a mix.
        // [prop1 => value1, prop2 => value2] becomes [[prop1 => value1], [prop2 => value2]].
        // [prop1 => value1, 0 => [prop2 => value2]] becomes [[prop1 => value1], [prop2 => value2]].
        $normFilters = [];
        foreach ($filters as $prop => $value) {
            if (is_string($prop)) {
                $normFilters[] = [$prop => $value];
            } else {
                $normFilters[] = $value;
            }
        }

        // Now we focus on each item, which should be a single condition.
        // Check if we have only single conditions.
        // [[prop1 => value1, prop2 => value2]] becomes [[prop1 => value1], [prop2 => value2]].
        $result = [];
        foreach ($normFilters as $condition) {
            $conditions = collect($condition)->map(function ($val, $prop) {
                return [$prop => $val];
            })->values()->all();
            $result = array_merge($result, $conditions);
        }

        return $result;
    }

    /**
     * Clear filters.
     *
     * @return void
     */
    protected function clearFilters()
    {
        $this->filters = [];
    }
}
