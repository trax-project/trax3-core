<?php

namespace Trax\Statements\Repos\Attachment;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
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
    protected $table = 'xapi_attachments';

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
    protected $fillable = ['id', 'content', 'content_type', 'length', 'store', 'client', 'stored'];
}
