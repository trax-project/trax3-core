<?php

namespace Trax\Statements\Http\Controllers;

use Trax\Framework\Http\Controllers\Controller;
use Trax\Framework\Http\Controllers\DeclareRetrievalApiRoutes;
use Trax\Framework\Http\Controllers\ImplementRetrievalApiMethods;
use Trax\Statements\Http\Requests\StatementApiRequest;
use Trax\Statements\Repos\Statement\StatementRepository;

class StatementApiController extends Controller
{
    use DeclareRetrievalApiRoutes, ImplementRetrievalApiMethods;

    /**
     * @var string
     */
    protected static $serviceKey = 'statements';

    /**
     * @var array
     */
    protected static $api = [
        'key' => 'statements',
        'domain' => 'statements',
        'subdomain' => 'exploration',
        'request' => StatementApiRequest::class,
    ];
    
    /**
     * Don't use dependency injection here has it may not work and called directly from gateway.
     * @return void
     */
    public function __construct()
    {
        $this->constructRetrievalApi();
        $this->repo = app(StatementRepository::class);
    }
}
