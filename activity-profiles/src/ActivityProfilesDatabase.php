<?php

namespace Trax\ActivityProfiles;

use Trax\Framework\Database\Database;
use Trax\ActivityProfiles\Repos\ActivityProfile\ActivityProfileRepository;
use Trax\ActivityProfiles\Recording\ActivityProfileRecorder;

class ActivityProfilesDatabase extends Database
{
    /**
     * @var string
     */
    protected $service = 'activity-profiles';
                        
    /**
     * @var string
     */
    protected static $connection = 'activity-profiles';
    
    /**
     * @var array
     */
    protected static $tables = [
        'xapi_activity_profiles' => ['type' => 'main'],
    ];

    /**
     * @var array
     */
    protected $storeTables = ['xapi_activity_profiles'];

    /**
     * @var bool
     */
    protected $nosqlSupported = true;

    /**
     * @var \Trax\ActivityProfiles\Repos\ActivityProfile\ActivityProfileRepository
     */
    protected $xapi_activity_profiles;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->xapi_activity_profiles = app(ActivityProfileRepository::class);
        $this->mainRepository = $this->xapi_activity_profiles;
        $this->recorder = app(ActivityProfileRecorder::class);
    }
}
