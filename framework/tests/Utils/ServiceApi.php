<?php

namespace Trax\Framework\Tests\Utils;

use Trax\Framework\Service\Config;

abstract class ServiceApi extends ServiceApiWithMiddleware
{
    public function setUp(): void
    {
        parent::setUp();

        // Disable async requests with databases such as Elasticsearch.
        Config::disableAsyncRequests();

        // We don't remove all the middlewares because we want to keep the SetContextMiddleware active.
        // We remove only the authentication middlewares.
        $this->withoutMiddleware([
            \Illuminate\Auth\Middleware\Authenticate::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Trax\Framework\Http\Middleware\ApiMiddleware::class,
        ]);
    }
}
