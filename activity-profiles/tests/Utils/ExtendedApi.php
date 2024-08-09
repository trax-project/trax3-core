<?php

namespace Trax\ActivityProfiles\Tests\Utils;

use Trax\Framework\Tests\Utils\ServiceApi;
use Trax\Framework\Service\Config;

class ExtendedApi extends ServiceApi
{
    protected $service = 'activity-profiles';

    public function setUp(): void
    {
        parent::setUp();

        if (!Config::extendedEdition()) {
            $this->markTestSkipped('Extended Edition disabled');
        }
    }
}
