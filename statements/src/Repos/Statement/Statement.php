<?php

namespace Trax\Statements\Repos\Statement;

use Illuminate\Database\Eloquent\Model;

class Statement extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'statements';

    /**
     * The table associated with the model.
     */
    protected $table = 'xapi_statements';

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
        'raw' => 'object',
        'voided' => 'boolean',
        'voiding' => 'boolean',
        'validated' => 'boolean',
        'valid' => 'boolean',
        'pseudonymized' => 'boolean',
        'agent_ids' => 'array',
        'activity_ids' => 'array',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'voided' => false,
        'voiding' => false,
        'validated' => false,
        'valid' => false,
        'pseudonymized' => false,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'raw', 'voided', 'voiding', 'validated', 'valid', 'pseudonymized',
        'actor_id', 'verb_id', 'object_id', 'type_id', 'agent_ids', 'activity_ids', 'registration', 'statement_ref',
        'store', 'client', 'stored', 'timestamp'
    ];
}
