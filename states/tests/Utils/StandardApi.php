<?php

namespace Trax\States\Tests\Utils;

use Trax\Framework\Tests\Utils\ServiceApi;
use Trax\States\Repos\State\StateRepository;

class StandardApi extends ServiceApi
{
    protected $service = 'states';

    protected $states;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->states = app(StateRepository::class);
    }
}
