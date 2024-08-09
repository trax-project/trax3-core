<?php

namespace Trax\Activities;

use Trax\Framework\Database\Database;
use Trax\Activities\Repos\Activity\ActivityRepository;
use Trax\Activities\Recording\ActivityRecorder;

class ActivitiesDatabase extends Database
{
    /**
     * @var string
     */
    protected $service = 'activities';
                            
    /**
     * @var string
     */
    protected static $connection = 'activities';
    
    /**
     * @var array
     */
    protected static $tables = [
        'xapi_activities' => ['type' => 'main'],
    ];

    /**
     * @var array
     */
    protected $storeTables = ['xapi_activities'];

    /**
     * @var bool
     */
    protected $nosqlSupported = true;

    /**
     * @var \Trax\Activities\Repos\Activity\ActivityRepository
     */
    protected $xapi_activities;
    
    /**
     * @return void
     */
    public function __construct()
    {
        $this->xapi_activities = app(ActivityRepository::class);
        $this->mainRepository = $this->xapi_activities;
        $this->recorder = app(ActivityRecorder::class);
    }
}
