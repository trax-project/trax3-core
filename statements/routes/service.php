<?php

// Micro-services.
\Trax\Framework\Service\MicroServiceController::routes('statements');

// Standard API.
\Trax\Statements\Http\Controllers\StandardApiController::routes();

// Extended API.
\Trax\Statements\Http\Controllers\StatementApiController::routes();
