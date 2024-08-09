<?php

namespace Trax\Framework\Xapi\Http\Requests;

use Trax\Framework\Context;
use Trax\Framework\Repo\CrudRequest;
use Trax\Framework\Repo\Query;
use Trax\Framework\Xapi\Http\Validation\AcceptAlternateRequests;

class XapiRequest extends CrudRequest
{
    use AcceptAlternateRequests;

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
        parent::__construct($params, $content, $contentType);

        // Extra validation (e.g. CMI5).
        Context::pipeline()->extraValidation($this);
    }

    /**
     * Get data to be recorded.
     *
     * @return array
     */
    public function data(): array
    {
        return ['data' => $this->content()];
    }

    /**
     * Get data to be associated with the log.
     *
     * @return array
     */
    public function logData(): array
    {
        if (empty($this->params())) {
            return [];
        }
        return ['params' => $this->params()];
    }

    /**
     * Get the matching query.
     *
     * @return \Trax\Framework\Repo\Query
     */
    public function query(): Query
    {
        // Query data.
        $query = [];

        // Params: we don't use directly $this->params because we don't want to change it.
        $params = $this->params;

        // Remove alternate params.
        foreach ($this->alternateInputs as $input) {
            unset($params[$input]);
        }

        // Others are used as filters.
        $query['filters'] = collect($params)->map(function ($val, $prop) {
            return [$prop => $val];
        })->values()->all();

        return new Query($query);
    }
}
