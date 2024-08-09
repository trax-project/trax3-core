<?php

namespace Trax\Agents\Repos\Agent;

use Illuminate\Database\Eloquent\Model;
use Trax\Framework\Xapi\Helpers\XapiAgent;

class Agent extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'agents';

    /**
     * The table associated with the model.
     */
    protected $table = 'xapi_agents';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'members' => 'array',
        'pseudonymized' => 'boolean',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'pseudonymized' => false,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'sid_field_1', 'sid_field_2', 'sid_type', 'name', 'is_group', 'members', 'members_count', 'pseudonymized', 'person_id', 'store', 'stored', 'updated',
    ];
}
