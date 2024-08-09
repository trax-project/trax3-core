<?php

namespace Trax\Framework\Service;

use Closure;
use Illuminate\Http\Request;

interface MicroServiceInterface
{
    /**
     * Check that all the service database is up and running.
     *
     * @return object|false
     */
    public function checkDatabase();

    /**
     * Get the service host.
     *
     * @return string
     *
     * @throws \Exception
     */
    public function host(): string;

    /**
     * Get the service endpoint.
     *
     * @param  string $path
     * @return string
     */
    public function endpoint(string $path = ''): string;

    /**
     * Check the service.
     *
     * @return object  (object) ['ready' => false, 'reason' => 'Missing endpoint']
     */
    public function check(): object;

    /**
     * Call a function of the service from the gateway.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $controllerClass
     * @param  string  $controllerMethod
     * @return \Illuminate\Http\Response
     */
    public function callFromGateway(Request $request, string $controllerClass, string $controllerMethod);

    /**
     * Get the service commands.
     *
     * @return array
     */
    public function commands();
}
