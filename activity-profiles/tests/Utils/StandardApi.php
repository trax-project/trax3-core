<?php

namespace Trax\ActivityProfiles\Tests\Utils;

use Trax\Framework\Tests\Utils\ServiceApi;
use Trax\ActivityProfiles\Repos\ActivityProfile\ActivityProfileRepository;

class StandardApi extends ServiceApi
{
    protected $service = 'activity-profiles';

    protected $profiles;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->profiles = app(ActivityProfileRepository::class);
    }
}
