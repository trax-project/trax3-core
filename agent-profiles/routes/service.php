<?php

use Trax\Framework\Service\Config;

// Micro-services.
\Trax\Framework\Service\MicroServiceController::routes('agent-profiles');

// Standard API.
\Trax\AgentProfiles\Http\Controllers\StandardApiController::routes();

// Extended API.
if (Config::extendedEdition()) {
    \Trax\AgentProfiles\Http\Controllers\AgentProfileApiController::routes();
}
