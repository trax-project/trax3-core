<?php

namespace Trax\States\Repos\State;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'states';

    /**
     * The table associated with the model.
     */
    protected $table = 'xapi_states';

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'activity_id', 'activity_iri', 'agent_id', 'agent_sid', 'state_id', 'registration', 'content', 'content_type', 'store', 'stored', 'updated',
    ];
}
