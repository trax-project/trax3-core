<?php

namespace Trax\States\Http\Controllers;

use Trax\Framework\Http\Controllers\Controller;
use Trax\Framework\Http\Controllers\DeclareRetrievalApiRoutes;
use Trax\Framework\Http\Controllers\ImplementRetrievalApiMethods;
use Trax\States\Http\Requests\StateApiRequest;
use Trax\States\Repos\State\StateRepository;

class StateApiController extends Controller
{
    use DeclareRetrievalApiRoutes, ImplementRetrievalApiMethods;

    /**
     * @var string
     */
    protected static $serviceKey = 'states';

    /**
     * @var array
     */
    protected static $api = [
        'key' => 'states',
        'domain' => 'states',
        'subdomain' => 'exploration',
        'request' => StateApiRequest::class,
    ];
    
    /**
     * Don't use dependency injection here has it may not work and called directly from gateway.
     * @return void
     */
    public function __construct()
    {
        $this->constructRetrievalApi();
        $this->repo = app(StateRepository::class);
    }
}
