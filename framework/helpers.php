<?php

use Illuminate\Support\Str;
use Trax\Framework\Xapi\Helpers\XapiDate;
use Trax\Framework\Service\Config;

if (!function_exists('traxGatewayUrl')) {
    /**
     * Get a service URL.
     *
     * @param  bool  $absolute
     * @return string
     */
    function traxGatewayUrl(bool $absolute = true): string
    {
        $relative = "/trax/api/gateway/front";
        return $absolute
            ? config('app.url') . $relative
            : $relative;
    }
}

if (!function_exists('traxStoreUrl')) {
    /**
     * Get a store service URL.
     *
     * @param  string  $store
     * @param  bool  $absolute
     * @return string
     */
    function traxStoreUrl(string $store = 'default', bool $absolute = true): string
    {
        $relative = "/trax/api/gateway/front/stores/$store";
        return $absolute
            ? config('app.url') . $relative
            : $relative;
    }
}

if (!function_exists('traxClientUrl')) {
    /**
     * Get a service URL.
     *
     * @param  string  $client
     * @param  bool  $absolute
     * @return string
     */
    function traxClientUrl(string $client = 'default', bool $absolute = true): string
    {
        $relative = "/trax/api/gateway/clients/$client";
        return $absolute
            ? config('app.url') . $relative
            : $relative;
    }
}

if (!function_exists('traxClientStoreUrl')) {
    /**
     * Get a store service URL.
     *
     * @param  string  $client
     * @param  string  $store
     * @param  bool  $absolute
     * @return string
     */
    function traxClientStoreUrl(string $client = 'default', string $store = 'default', bool $absolute = true): string
    {
        $relative = "/trax/api/gateway/clients/$client/stores/$store";
        return $absolute
            ? config('app.url') . $relative
            : $relative;
    }
}

if (!function_exists('traxDeclareRepositoryClass')) {
    /**
     * Declare a repository class.
     *
     * @param  string  $namespace
     * @param  string  $serviceKey
     * @return void
     */
    function traxDeclareRepositoryClass(string $namespace, string $serviceKey): void
    {
        $driver = Config::databaseSettings($serviceKey)->driver;

        $code = "namespace $namespace; ";
        if ($driver == 'mongodb') {
            $code .= 'abstract class Repository extends \Trax\Extensions\Repo\Mongo\MongoRepository {}';
        } elseif ($driver == 'elasticsearch') {
            $code .= 'abstract class Repository extends \Trax\Extensions\Repo\Elastic\ElasticRepository {}';
        } elseif ($driver == 'opensearch') {
            $code .= 'abstract class Repository extends \Trax\Extensions\Repo\OpenSearch\OpenSearchRepository {}';
        } else {
            $code .= 'abstract class Repository extends \Trax\Framework\Repo\Eloquent\EloquentRepository {}';
        }

        //Repository::$driver = null;
        
        eval($code);
    }
}

if (!function_exists('traxConfigFromFile')) {
    /**
     * Return the config extracted from a config file, given custom and local locations.
     *
     * @param  string  $customLocation
     * @param  string  $defaultLocation
     * @return array
     */
    function traxConfigFromFile(string $customLocation, string $defaultLocation): array
    {
        // Custom file first.
        $custom = base_path($customLocation);
        if (file_exists($custom)) {
            return include $custom;
        }
        
        // Default file.
        $default = base_path($defaultLocation);
        if (file_exists($default)) {
            return include $default;
        }

        return [];
    }
}

if (!function_exists('traxUuid')) {
    /**
     * Return an UUID.
     *
     * @return string
     */
    function traxUuid()
    {
        return Str::uuid()->toString();
    }
}

if (!function_exists('traxNow')) {
    /**
     * Return the current timestamp.
     *
     * @return string
     */
    function traxNow()
    {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('traxIsoNow')) {
    /**
     * Return the current ISO timestamp.
     *
     * @return string
     */
    function traxIsoNow()
    {
        return XapiDate::now();
    }
}
