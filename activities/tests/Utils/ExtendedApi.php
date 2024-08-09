<?php

namespace Trax\Activities\Tests\Utils;

use Trax\Framework\Tests\Utils\ServiceApi;
use Trax\Framework\Service\Config;

class ExtendedApi extends ServiceApi
{
    protected $service = 'activities';

    public function setUp(): void
    {
        parent::setUp();

        if (!Config::extendedEdition()) {
            $this->markTestSkipped('Extended Edition disabled');
        }
    }
}
