<?php

namespace Trax\Framework\Repo;

use Trax\Framework\Repo\Query;

class CrudRequest
{
    /**
     * Request params.
     *
     * @var array
     */
    protected $params;

    /**
     * Request content.
     *
     * @var object|array
     */
    protected $content;

    /**
     * Request content type.
     *
     * @var string
     */
    protected $contentType;

    /**
     * Params casting.
     *
     * @var array
     */
    protected $casts = [
        'after' => 'array',
        'before' => 'array',
        'filters' => 'array',
        'options' => 'array',
        'distinct' => 'array',
    ];

    /**
     * Make a request.
     *
     * @param  array  $params
     * @param  object|array|null  $content
     * @param  string|null  $contentType
     * @return void
     */
    public function __construct(array $params, $content = null, $contentType = null)
    {
        $this->params = $this->castParams($params);
        $this->content = $content;
        $this->contentType = $contentType;
    }

    /**
     * Return the validation rules of an HTTP query.
     *
     * @return array
     */
    public static function validationRules()
    {
        return [
            'relations' => 'array',
            'accessors' => 'array',
            'sort' => 'array',
            'scope' => 'string',
            'limit' => 'integer|min:1',
            'skip' => 'integer|min:0',
            'after' => 'array_or_json',
            'before' => 'array_or_json',
            'filters' => 'array_or_json',
            'options' => 'array_or_json',
            'distinct' => 'array',
        ];
    }

    /**
     * Get the params.
     *
     * @return array
     */
    public function params(): array
    {
        return $this->params;
    }

    /**
     * Check if there is a param.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasParam(string $name): bool
    {
        return isset($this->params[$name]);
    }

    /**
     * Get a param.
     *
     * @param  string  $name
     * @return mixed
     */
    public function param(string $name)
    {
        return $this->hasParam($name) ? $this->params[$name] : null;
    }

    /**
     * Add a param.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @return void
     */
    public function addParam(string $name, $value)
    {
        if ($this->hasParam($name) && is_array($this->params[$name])) {
            $this->params[$name] = array_merge($this->params[$name], $value);
        } else {
            $this->params[$name] = $this->castParam($name, $value);
        }
    }

    /**
     * Remove a param.
     *
     * @param  string  $name
     * @return void
     */
    public function removeParam(string $name)
    {
        unset($this->params[$name]);
    }

    /**
     * Get the scope.
     *
     * @return string|null
     */
    public function scope()
    {
        if (!$this->hasParam('scope')) {
            return null;
        }
        return $this->param('scope');
    }

    /**
     * Get an option.
     *
     * @param  string  $name
     * @return mixed
     */
    public function option(string $name)
    {
        if (!$this->hasParam('options')) {
            return null;
        }
        $options = $this->param('options');
        if (!isset($options[$name])) {
            return null;
        }
        return $options[$name];
    }

    /**
     * Get filters.
     *
     * @return array
     */
    public function filters()
    {
        return $this->param('filters');
    }

    /**
     * Add a filter.
     *
     * @param  mixed  $filter
     * @return void
     */
    public function addFilter($filter)
    {
        if (!$this->hasParam('filters')) {
            $this->params['filters'] = [];
        }
        $this->params['filters'][] = $filter;
    }

    /**
     * Remove filters.
     *
     * @return void
     */
    public function removeFilters()
    {
        $this->removeParam('filters');
    }

    /**
     * Get the content.
     *
     * @param  bool  $encode
     * @return object|array|string|null
     */
    public function content(bool $encode = false)
    {
        if (is_null($this->content)) {
            return null;
        }
        if ($encode && !is_string($this->content)) {
            return json_encode($this->content);
        }
        return $this->content;
    }

    /**
     * Get the content type.
     *
     * @return string
     */
    public function contentType()
    {
        return $this->contentType;
    }

    /**
     * Check if there is a given content field.
     *
     * @param  string  $field
     * @return bool
     */
    public function hasContentField(string $field): bool
    {
        return isset($this->content[$field]);
    }

    /**
     * Get a given content field.
     *
     * @param string  $field
     * @return mixed
     */
    public function contentField(string $field)
    {
        return $this->hasContentField($field) ? $this->content[$field] : null;
    }

    /**
     * Set the content.
     *
     * @param object|array  $content
     * @return void
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Set a content field.
     *
     * @param string  $field
     * @param mixed  $value
     * @return void
     */
    public function setContentField(string $field, $value)
    {
        $this->content[$field] = $value;
    }

    /**
     * Remove a content field.
     *
     * @param string  $field
     * @param mixed  $value
     * @return void
     */
    public function unsetContentField(string $field)
    {
        unset($this->content[$field]);
    }

    /**
     * Get the matching query.
     *
     * @return \Trax\Framework\Repo\Query
     */
    public function query(): Query
    {
        return new Query($this->params);
    }

    /**
     * Cast the params.
     *
     * @param  array  $params
     * @return array
     */
    protected function castParams(array $params): array
    {
        return collect($params)->transform(function ($value, $name) {
            return $this->castParam($name, $value);
        })->toArray();
    }

    /**
     * Cast a param.
     *
     * @param  string  $name
     * @param  mixed  $value
     * @return mixed
     */
    protected function castParam(string $name, $value)
    {
        if (!isset($this->casts[$name])) {
            return $value;
        }

        // JSON string to array.
        if ($this->casts[$name] == 'array' && is_string($value)) {
            return json_decode($value, true);
        }

        return $value;
    }
}
