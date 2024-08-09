<?php

namespace Trax\Framework\Repo;

use Illuminate\Support\Str;

class Query
{
    use HasFilters;

    /**
     * With relations.
     *
     * @var array
     */
    protected $with = [];

    /**
     * Append accessors.
     *
     * @var array
     */
    protected $append = [];

    /**
     * Sorting.
     *
     * @var array
     */
    protected $sort = [];

    /**
     * Pagination: default limit.
     *
     * @var int
     */
    protected $defaultLimit = 100;

    /**
     * Pagination: limit.
     *
     * @var int
     */
    protected $limit = 0;

    /**
     * Pagination: offset.
     *
     * @var int
     */
    protected $skip = 0;

    /**
     * Pagination: after.
     *
     * @var array
     */
    protected $after = [];

    /**
     * Pagination: before.
     *
     * @var array
     */
    protected $before = [];

    /**
     * Options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Distinct.
     *
     * @var array
     */
    protected $distinct = [];

    /**
     * Query properties.
     *
     * @var array
     */
    protected $queryParams = [
        'filters' => 'filters',
        'relations' => 'with',
        'accessors' => 'append',
        'sort' => 'sort',
        'limit' => 'limit',
        'skip' => 'skip',
        'after' => 'after',
        'before' => 'before',
        'options' => 'options',
        'distinct' => 'distinct',
    ];

    /**
     * Query keywords.
     *
     * @var array
     */
    protected static $keywords = [
        '$eq', '$gt', '$gte', '$lt', '$lte', '$ne',
        '$in', '$nin',
        '$text',
        '$exists',
        '$has',
        '$or', '$and',
    ];

    /**
     * Constructor.
     *
     * @param array  $params
     * @return void
     */
    public function __construct(array $params = [])
    {
        foreach ($this->queryParams as $name => $prop) {
            if (isset($params[$name])) {
                $this->$prop = $params[$name];
            }
        }
    }

    /**
     * Get the keywords.
     *
     * @return array
     */
    public static function keywords(): array
    {
        return self::$keywords;
    }

    /**
     * Get with.
     *
     * @return array
     */
    public function with(): array
    {
        return $this->with;
    }

    /**
     * Get append.
     *
     * @return array
     */
    public function append(): array
    {
        return $this->append;
    }

    /**
     * Is sorted?
     *
     * @return bool
     */
    public function sorted(): bool
    {
        return !empty($this->sort);
    }

    /**
     * Get sort info: [[column, direction, relation], ...].
     *
     * @return array
     */
    public function sortInfo(): array
    {
        $sortInfo = [];
        foreach ($this->sort as $sort) {
            $dir = 'asc';
            $col = $sort;
            if (Str::startsWith($col, '-')) {
                $dir = 'desc';
                $col = Str::after($col, '-');
            }
            $colParts = explode('.', $col);
            if (count($colParts) == 1) {
                $sortInfo[] = ['col' => $col, 'dir' => $dir, 'rel' => null];
            } else {
                $sortInfo[] = ['col' => $colParts[1], 'dir' => $dir, 'rel' => $colParts[0]];
            }
        }
        return $sortInfo;
    }

    /**
     * Has before info?
     *
     * @return bool
     */
    public function hasBefore(): bool
    {
        return !empty($this->before);
    }

    /**
     * Get before info: [column, value].
     *
     * @return array
     */
    public function beforeInfo(): array
    {
        foreach ($this->before as $col => $val) {
            break;
        }
        return [$col, $val];
    }

    /**
     * Has after info?
     *
     * @return bool
     */
    public function hasAfter(): bool
    {
        return !empty($this->after);
    }

    /**
     * Get before info: [column, value].
     *
     * @return array
     */
    public function afterInfo(): array
    {
        foreach ($this->after as $col => $val) {
            break;
        }
        return [$col, $val];
    }

    /**
     * Get limit.
     *
     * @return int
     */
    public function limit(): int
    {
        return intval($this->limit) ? intval($this->limit) : $this->defaultLimit;
    }

    /**
     * Has limit.
     *
     * @return bool
     */
    public function hasLimit(): bool
    {
        return intval($this->limit) > 0;
    }

    /**
     * Set limit.
     *
     * @param  int  $limit
     * @return void
     */
    public function setLimit($limit): void
    {
        $this->limit = $limit;
    }

    /**
     * Get skip.
     *
     * @return int
     */
    public function skip(): int
    {
        return intval($this->skip);
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function options(): array
    {
        return $this->options;
    }

    /**
     * Has a given option?
     *
     * @param  string  $name
     * @return bool
     */
    public function hasOption(string $name): bool
    {
        return isset($this->options[$name]);
    }

    /**
     * Get a given option.
     *
     * @param  string  $name
     * @return mixed
     */
    public function option(string $name, $default = null)
    {
        if (!$this->hasOption($name)) {
            return $default;
        }
        return $this->options[$name];
    }

    /**
     * Get distinct.
     *
     * @return array
     */
    public function distinct(): array
    {
        return $this->distinct;
    }
}
