<?php

use Trax\Framework\Service\Config;

// Micro-services.
\Trax\Framework\Service\MicroServiceController::routes('activity-profile');

// Standard API.
\Trax\ActivityProfiles\Http\Controllers\StandardApiController::routes();

// Extended API.
if (Config::extendedEdition()) {
    \Trax\ActivityProfiles\Http\Controllers\ActivityProfileApiController::routes();
}
