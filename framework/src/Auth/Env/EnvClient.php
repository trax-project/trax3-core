<?php

namespace Trax\Framework\Auth\Env;

use Trax\Framework\Auth\Client;
use Trax\Framework\Xapi\Helpers\XapiPipeline;

class EnvClient implements Client
{
    /**
     * @var int
     */
    public $id = 1;
    
    /**
     * @var string
     */
    public $slug = 'default';
    
    /**
     * @var string
     */
    public $name = 'Default client';
    
    /**
     * @var string
     */
    public $guard = 'basic-http';
    
    /**
     * @var string
     */
    public $cors = '*';
    
    /**
     * @var string
     */
    public $store = 'default';
    
    /**
     * @var array
     */
    public $permissions = ["xapi/all" => true];

    /**
     * Return the client pipeline.
     *
     * @return \Trax\Framework\Xapi\Helpers\XapiPipeline
     */
    public function pipeline(): XapiPipeline
    {
        return new XapiPipeline;
    }

    /**
     * Return the client settings.
     *
     * @return object
     */
    public function settings(): object
    {
        return (object)[];
    }

    /**
     * Return the xAPI endpoint.
     *
     * @return string
     */
    public function xapiEndpoint(): string
    {
        return traxClientStoreUrl($this->slug, $this->store) . '/xapi';
    }

    /**
     * Return the client credentials.
     *
     * @return object
     */
    public function credentials(): object
    {
        return (object)[
            'username' => config('trax.auth.endpoint.username'),
            'password' => config('trax.auth.endpoint.password'),
        ];
    }

    /**
     * Return the client guard.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function checkCredentials(array $credentials): bool
    {
        return $credentials['username'] === config('trax.auth.endpoint.username')
            && $credentials['password'] === config('trax.auth.endpoint.password');
    }

    /**
     * Get the client capabilities.
     *
     * @return array
     */
    public function capabilities(): array
    {
        return [];
    }
}
