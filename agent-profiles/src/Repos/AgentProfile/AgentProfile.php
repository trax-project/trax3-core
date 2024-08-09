<?php

namespace Trax\AgentProfiles\Repos\AgentProfile;

use Illuminate\Database\Eloquent\Model;

class AgentProfile extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'agent-profiles';

    /**
     * The table associated with the model.
     */
    protected $table = 'xapi_agent_profiles';

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
        'id', 'agent_id', 'agent_sid', 'profile_id', 'content', 'content_type', 'store', 'stored', 'updated',
    ];
}
