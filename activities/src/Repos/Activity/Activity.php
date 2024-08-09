<?php

namespace Trax\Activities\Repos\Activity;

use Illuminate\Database\Eloquent\Model;
use Trax\Framework\Xapi\Helpers\XapiActivity;

class Activity extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'activities';

    /**
     * The table associated with the model.
     */
    protected $table = 'xapi_activities';

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
        'definition' => 'object',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'iri', 'definition', 'type_id', 'is_category', 'is_profile', 'store', 'stored', 'updated',
    ];
}
