<?php

use Illuminate\Support\Facades\Route;
use Trax\Framework\Service\Config;
use Trax\Framework\Http\Routing;

// Micro-services.
\Trax\Framework\Service\MicroServiceController::routes('gateway');

// Standard API.
Route::get('/trax/api/gateway/clients/{xclient}/stores/{xstore}/xapi/about', [
    \Trax\Framework\Xapi\Http\Controllers\AboutController::class,
    'get'
]);

\Trax\Statements\Http\Controllers\StandardApiController::gatewayRoutes(Routing::API_ONLY);
\Trax\Activities\Http\Controllers\StandardApiController::gatewayRoutes(Routing::API_ONLY);
\Trax\Agents\Http\Controllers\StandardApiController::gatewayRoutes(Routing::API_ONLY);
\Trax\ActivityProfiles\Http\Controllers\StandardApiController::gatewayRoutes(Routing::API_ONLY);
\Trax\AgentProfiles\Http\Controllers\StandardApiController::gatewayRoutes(Routing::API_ONLY);
\Trax\States\Http\Controllers\StandardApiController::gatewayRoutes(Routing::API_ONLY);

// Data API.
\Trax\Statements\Http\Controllers\StatementApiController::gatewayRoutes(Routing::dataApiMode());

// Extended Edition.
if (Config::extendedEdition()) {

    // Data API.
    \Trax\Activities\Http\Controllers\ActivityApiController::gatewayRoutes(Routing::dataApiMode());
    \Trax\Agents\Http\Controllers\AgentApiController::gatewayRoutes(Routing::dataApiMode());
    \Trax\ActivityProfiles\Http\Controllers\ActivityProfileApiController::gatewayRoutes(Routing::dataApiMode());
    \Trax\AgentProfiles\Http\Controllers\AgentProfileApiController::gatewayRoutes(Routing::dataApiMode());
    \Trax\States\Http\Controllers\StateApiController::gatewayRoutes(Routing::dataApiMode());

    \Trax\Agents\Http\Controllers\PersonApiController::gatewayRoutes(Routing::dataApiMode());

    \Trax\Vocab\Http\Controllers\TypeApiController::gatewayRoutes(Routing::dataApiMode());
    \Trax\Vocab\Http\Controllers\VerbApiController::gatewayRoutes(Routing::dataApiMode());
    \Trax\Vocab\Http\Controllers\DocumentIdApiController::gatewayRoutes(Routing::dataApiMode());
    \Trax\Vocab\Http\Controllers\ActivityIdApiController::gatewayRoutes(Routing::dataApiMode());
    \Trax\Vocab\Http\Controllers\AgentIdApiController::gatewayRoutes(Routing::dataApiMode());

    // Jobs API.
    \Trax\Commands\Jobs\Http\LogsDeletionApiController::gatewayRoutes(Routing::jobsApiMode());
    \Trax\Commands\Jobs\Http\ValidationApiController::gatewayRoutes(Routing::jobsApiMode());
    \Trax\Commands\Jobs\Http\PseudoApiController::gatewayRoutes(Routing::jobsApiMode());
    \Trax\Commands\Jobs\Http\PullApiController::gatewayRoutes(Routing::jobsApiMode());
    \Trax\Commands\Jobs\Http\PushApiController::gatewayRoutes(Routing::jobsApiMode());
    \Trax\Commands\Jobs\Http\ClearingApiController::gatewayRoutes(Routing::jobsApiMode());
    \Trax\Commands\Jobs\Http\AgentDeletionApiController::gatewayRoutes(Routing::jobsApiMode());
    \Trax\Commands\Jobs\Http\SeedingApiController::gatewayRoutes(Routing::jobsApiMode());
    \Trax\Commands\Jobs\Http\TestsuiteApiController::gatewayRoutes(Routing::jobsApiMode());
    \Trax\Commands\Jobs\Http\StoresDeletionApiController::gatewayRoutes(Routing::jobsApiMode());
    \Trax\Commands\Jobs\Http\Cmi5TokensDeletionApiController::gatewayRoutes(Routing::jobsApiMode());

    // Logs API.
    \Trax\Logging\Http\LogApiController::gatewayRoutes(Routing::loggingApiMode());
    \Trax\Logging\Http\LogChannelApiController::gatewayRoutes(Routing::loggingApiMode());

    // Access API.
    \Trax\Auth\Http\Controllers\StoreApiController::gatewayRoutes(Routing::accessApiMode());
    \Trax\Auth\Http\Controllers\ClientApiController::gatewayRoutes(Routing::accessApiMode());
    \Trax\Auth\Http\Controllers\UserApiController::gatewayRoutes(Routing::accessApiMode());

    \Trax\Auth\Http\Controllers\Cmi5ApiController::gatewayRoutes(Routing::API_ONLY);
    
    \Trax\Auth\Http\Controllers\LrsApiController::gatewayRoutes(Routing::accessApiMode());
    \Trax\Auth\Http\Controllers\DbApiController::gatewayRoutes(Routing::accessApiMode());
    \Trax\Auth\Http\Controllers\FileApiController::gatewayRoutes(Routing::accessApiMode());
}
