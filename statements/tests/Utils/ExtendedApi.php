<?php

namespace Trax\Statements\Tests\Utils;

use Trax\Framework\Tests\Utils\ServiceApi;

class ExtendedApi extends ServiceApi
{
    protected $service = 'statements';
    
    public function setUp(): void
    {
        parent::setUp();
    }
}
