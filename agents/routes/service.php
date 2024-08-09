<?php

use Trax\Framework\Service\Config;

// Micro-services.
\Trax\Framework\Service\MicroServiceController::routes('agents');

// Standard API.
\Trax\Agents\Http\Controllers\StandardApiController::routes();

// Extended API.
if (Config::extendedEdition()) {
    \Trax\Agents\Http\Controllers\AgentApiController::routes();
    \Trax\Agents\Http\Controllers\PersonApiController::routes();
}
