<?php

namespace Trax\Framework\Xapi\Helpers;

use Trax\Framework\Xapi\Http\Requests\XapiRequest;

class XapiPipeline
{
    /**
     * @var bool
     */
    public $validate_statements;

    /**
     * @var bool
     */
    public $check_conflicts;

    /**
     * @var bool
     */
    public $record_attachments;

    /**
     * @var bool
     */
    public $void_statements;

    /**
     * @var bool
     */
    public $update_activities;

    /**
     * @var bool
     */
    public $update_agents;

    /**
     * @var bool
     */
    public $update_vocab;

    /**
     * @var bool
     */
    public $update_activity_ids;

    /**
     * @var bool
     */
    public $update_agent_ids;

    /**
     * @var bool
     */
    public $pseudonymize_statements;

    /**
     * @var bool
     */
    public $query_targeting;

    /**
     * @var array
     */
    public $authority;

    /**
     * @var string|null
     */
    public $extra_validation_class = null;

    /**
     * @var string|null
     */
    public $transformation_class = null;

    /**
     * @var string|null
     */
    public $filtering_class = null;

    /**
     * @param  array  $props
     * @return void
     */
    public function __construct(array $props = [])
    {
        // Set the defaults.
        foreach ([
            'validate_statements',
            'check_conflicts',
            'record_attachments',
            'void_statements',
            'update_activities',
            'update_agents',
            'update_vocab',
            'update_activity_ids',
            'update_agent_ids',
            'pseudonymize_statements',
            'query_targeting',
        ] as $prop) {
            $this->$prop = config("trax.pipeline.$prop.default");
        }

        // Assign the provided config, except for props which are forced to the default.
        foreach ($props as $prop => $value) {
            if (!config("trax.pipeline.$prop.forced")) {
                $this->$prop = $value;
            }
        }
        
        $this->authority = isset($props['authority'])
            ? json_decode(json_encode($props['authority']))
            : json_decode(json_encode(config('trax.xapi.authority')));

        if (isset($props['extra_validation_class'])) {
            $this->extra_validation_class = $props['extra_validation_class'];
        }

        if (isset($props['transformation_class'])) {
            $this->transformation_class = $props['transformation_class'];
        }

        if (isset($props['filtering_class'])) {
            $this->filtering_class = $props['filtering_class'];
        }
    }

    /**
     * @return array
     */
    public function serialize(): array
    {
        return get_object_vars($this);
    }

    /**
     * @param  \Trax\Framework\Xapi\Http\Requests\XapiRequest  $xapiRequest
     * @return void
     */
    public function extraValidation(XapiRequest $xapiRequest): void
    {
        if (!empty($this->extra_validation_class)) {
            (new $this->extra_validation_class)->validate($xapiRequest);
        }
    }
}
