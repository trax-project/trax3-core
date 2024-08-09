<?php

use Trax\Framework\Service\Config;

// Micro-services.
\Trax\Framework\Service\MicroServiceController::routes('states');

// Standard API.
\Trax\States\Http\Controllers\StandardApiController::routes();

// Extended API.
if (Config::extendedEdition()) {
    \Trax\States\Http\Controllers\StateApiController::routes();
}
