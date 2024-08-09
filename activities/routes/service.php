<?php

use Trax\Framework\Service\Config;

// Micro-services.
\Trax\Framework\Service\MicroServiceController::routes('activities');

// Standard API.
\Trax\Activities\Http\Controllers\StandardApiController::routes();

// Extended API.
if (Config::extendedEdition()) {
    \Trax\Activities\Http\Controllers\ActivityApiController::routes();
}
