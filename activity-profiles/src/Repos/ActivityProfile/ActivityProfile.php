<?php

namespace Trax\ActivityProfiles\Repos\ActivityProfile;

use Illuminate\Database\Eloquent\Model;

class ActivityProfile extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'activity-profiles';

    /**
     * The table associated with the model.
     */
    protected $table = 'xapi_activity_profiles';

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
        'id', 'activity_id', 'activity_iri', 'profile_id', 'content', 'content_type', 'store', 'stored', 'updated',
    ];
}
