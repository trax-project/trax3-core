<?php

namespace Trax\Activities\Tests\Utils;

use Trax\Framework\Tests\Utils\ServiceApi;
use Trax\Activities\Repos\Activity\ActivityRepository;

class StandardApi extends ServiceApi
{
    protected $service = 'activities';

    protected $activities;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->activities = app(ActivityRepository::class);
    }
}
