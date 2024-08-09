<?php

namespace Trax\Framework\Auth;

use Trax\Framework\Xapi\Helpers\XapiPipeline;

interface Client
{
    /**
     * Return the client pipeline.
     *
     * @return \Trax\Framework\Xapi\Helpers\XapiPipeline
     */
    public function pipeline(): XapiPipeline;

    /**
     * Return the client settings.
     *
     * @return object
     */
    public function settings(): object;

    /**
     * Return the xAPI endpoint.
     *
     * @return string
     */
    public function xapiEndpoint(): string;

    /**
     * Return the client credentials.
     *
     * @return object
     */
    public function credentials(): object;

    /**
     * Check the client credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function checkCredentials(array $credentials): bool;

    /**
     * Get the client capabilities.
     *
     * @return array
     */
    public function capabilities(): array;
}
