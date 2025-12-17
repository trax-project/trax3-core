<?php

namespace Trax\Framework\Tests\Utils;

use Illuminate\Support\Str;
use Trax\Framework\Tests\TestCase;
use Trax\Framework\Service\Config;

abstract class ServiceApiWithMiddleware extends TestCase
{
    use RefreshDatabase, RawRequests;

    protected $service = 'TO BE DEFINED';

    protected $api = 'TO BE DEFINED';
    
    protected function apiEndpoint(string $api, array $params = []): string
    {
        return traxGatewayUrl()
            . "/$api"
            . (empty($params) ? '' : '?' . http_build_query($params));
    }
    
    protected function storeApiEndpoint(string $api, array $params = [], $store = 'default'): string
    {
        if (Str::of($api)->startsWith('xapi/')) {
            return $this->clientStoreApiEndpoint($api, $params, 'default', $store);
        }
        return traxStoreUrl($store)
            . "/$api"
            . (empty($params) ? '' : '?' . http_build_query($params));
    }

    protected function clientApiEndpoint(string $api, array $params = [], $client = 'default'): string
    {
        return traxClientUrl($client)
            . "/$api"
            . (empty($params) ? '' : '?' . http_build_query($params));
    }
    
    protected function clientStoreApiEndpoint(string $api, array $params = [], $client = 'default', $store = 'default'): string
    {
        return traxClientStoreUrl($client, $store)
            . "/$api"
            . (empty($params) ? '' : '?' . http_build_query($params));
    }

    protected function isLocalService(): bool
    {
        return Config::isLocalService($this->service);
    }

    protected function isRemoteService(): bool
    {
        return Config::isRemoteService($this->service);
    }
}
