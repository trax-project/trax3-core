<?php

namespace Trax\Framework;

use Trax\Framework\Xapi\Helpers\XapiPipeline;
use Trax\Agents\Repos\Agent\AgentRepository;
use Trax\Framework\Auth\Client;
use Trax\Framework\Auth\User;
use Trax\Framework\Xapi\Helpers\XapiAgent;

class Context
{
    /**
     * @var string
     */
    protected static $origin = 'api';

    /**
     * @var string
     */
    protected static $service = null;

    /**
     * @var string
     */
    protected static $api = null;

    /**
     * @var string
     */
    protected static $method = null;

    /**
     * @var string
     */
    protected static $store = null;

    /**
     * @var string
     */
    protected static $client = null;

    /**
     * @var \Trax\Framework\Xapi\Helpers\XapiPipeline
     */
    protected static $pipeline = null;

    /**
     * @var object
     */
    protected static $user = null;

    /**
     * @var string
     */
    protected static $consumer = null;

    /**
     * @var array
     */
    protected static $stores = [];

    /**
     * @var array
     */
    protected static $capabilities = [];

    /**
     * @var object
     */
    protected static $settings = null;

    /**
     * Reset the context and set the current entry point.
     *
     * @param  string  $service
     * @param  string  $api
     * @param  string  $method
     * @return void
     */
    public static function setEntryPoint(string $service = null, string $api = null, string $method = null): void
    {
        self::setService($service);
        self::setApi($api);
        self::setMethod($method);
    }

    /**
     * Set the current origin (api or console).
     *
     * @param  string  $origin
     * @return void
     */
    public static function setOrigin(string $origin = 'api'): void
    {
        self::$origin = $origin;
    }

    /**
     * Set the current service (usually override it).
     *
     * @param  string  $service
     * @return void
     */
    public static function setService(string $service = null): void
    {
        self::$service = $service;
    }

    /**
     * Set the current API (usually override it).
     *
     * @param  string  $api
     * @return void
     */
    public static function setApi(string $api = null): void
    {
        self::$api = $api;
    }

    /**
     * Set the current method (usually override it).
     *
     * @param  string  $method
     * @return void
     */
    public static function setMethod(string $method = null): void
    {
        self::$method = $method;
    }

    /**
     * Set the store.
     *
     * @param  string  $slug
     * @return void
     */
    public static function setStore(string $slug = null): void
    {
        self::$store = $slug;
    }

    /**
     * Set the current client.
     *
     * @param  \Trax\Framework\Auth\Client  $client
     * @return void
     */
    public static function setClient(Client $client): void
    {
        self::$client = $client->slug;
        self::setPipeline($client->pipeline());
        self::$consumer = 'client';
        self::$stores = [$client->store];
        self::$capabilities = $client->capabilities();
        self::$settings = $client->settings();
    }

    /**
     * Set the current pipeline.
     *
     * @param  \Trax\Framework\Xapi\Helpers\XapiPipeline|array|null  $pipeline
     * @return void
     */
    public static function setPipeline($pipeline = null): void
    {
        if (!isset($pipeline)) {
            // Default pipeline.
            self::$pipeline = new XapiPipeline;
        } elseif (is_array($pipeline)) {
            // Array: we must deserialize.
            self::$pipeline = new XapiPipeline($pipeline);
        } else {
            // XapiPipeline: we set it.
            self::$pipeline = $pipeline;
        }
    }

    /**
     * Set the current user.
     *
     * @param  \Trax\Framework\Auth\User  $user
     * @return void
     */
    public static function setUser(User $user): void
    {
        self::$consumer = 'user';

        // We need the person in the context because the person is used to determine de "mine" scope in some repositories.
        // However, this is only needed under store routes, not in general routes.
        if (empty(self::store()) || empty($user->agent)) {
            $person = collect();
        } else {
            $person = app(AgentRepository::class)->getPersonAgents(
                json_decode($user->agent, true)
            );
        }

        self::$user = (object)[
            'id' => $user->id,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'agent' => $user->agent,
            'role' => $user->role,
            'person_sids' => $person->map(function ($agent) {
                return XapiAgent::stringId($agent);
            })->all(),
            'person_hids' => $person->map(function ($agent) {
                return XapiAgent::hashId($agent);
            })->all(),
            'stores' => $user->store_slugs,
        ];

        self::$stores = $user->store_slugs;
        self::$capabilities = $user->capabilities();
    }

    /**
     * Get the current origin.
     *
     * @return string
     */
    public static function origin()
    {
        return self::$origin;
    }

    /**
     * Get the current service.
     *
     * @return string|null
     */
    public static function service()
    {
        return self::$service;
    }

    /**
     * Get the current API.
     *
     * @return string|null
     */
    public static function api()
    {
        return self::$api;
    }

    /**
     * Get the current method.
     *
     * @return string|null
     */
    public static function method()
    {
        return self::$method;
    }

    /**
     * Get the current store slug.
     *
     * @return string|null
     */
    public static function store()
    {
        return self::$store;
    }

    /**
     * Get the current client slug.
     *
     * @return string|null
     */
    public static function client()
    {
        return self::$client;
    }

    /**
     * Get the current pipeline.
     *
     * @return \Trax\Framework\Xapi\Helpers\XapiPipeline
     */
    public static function pipeline(): XapiPipeline
    {
        if (!empty(self::$pipeline)) {
            return self::$pipeline;
        }
        return new XapiPipeline;
    }

    /**
     * Get the type of consumer.
     *
     * @return string|null
     */
    public static function consumer()
    {
        return self::$consumer;
    }

    /**
     * Get the user object.
     *
     * @return object|null
     */
    public static function user()
    {
        return self::$user;
    }

    /**
     * Get the consumer stores.
     *
     * @return array
     */
    public static function stores(): array
    {
        return self::$stores;
    }

    /**
     * Get the consumer capabilities.
     *
     * @return array
     */
    public static function capabilities(): array
    {
        return self::$capabilities;
    }

    /**
     * Get the consumer settings.
     *
     * @return object|null
     */
    public static function settings()
    {
        return self::$settings;
    }

    /**
     * Get the context serialized data.
     *
     * @return array
     */
    public static function serialize(): array
    {
        return [
            'origin' => self::origin(),
            'service' => self::service(),
            'api' => self::api(),
            'method' => self::method(),
            'store' => self::store(),
            'client' => self::client(),
            'pipeline' => self::pipeline()->serialize(),
            'consumer' => self::consumer(),
            'user' => (array) self::user(),
            'stores' => self::stores(),
            'capabilities' => self::capabilities(),
            'settings' => self::settings(),
        ];
    }

    /**
     * Restore the context from the serialized data.
     *
     * @param  array  $data
     * @return void
     */
    public static function unserialize(array $data): void
    {
        self::$origin = $data['origin'];
        self::$service = $data['service'];
        self::$api = $data['api'];
        self::$method = $data['method'];
        self::$store = $data['store'];
        self::$client = $data['client'];
        self::setPipeline($data['pipeline']);
        self::$consumer = $data['consumer'];
        self::$user = (object) $data['user'];
        self::$stores = $data['stores'];
        self::$capabilities = $data['capabilities'];
        self::$settings = $data['settings'];
    }

    /**
     * Reset the context.
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$origin = 'api';
        self::$service = null;
        self::$api = null;
        self::$method = null;
        self::$store = null;
        self::$client = null;
        self::setPipeline();
        self::$consumer = null;
        self::$user = null;
        self::$stores = [];
        self::$capabilities = [];
        self::$settings = null;
    }

    /**
     * Get the context serialized, ready to be inserted in a header.
     *
     * @return string
     */
    public static function getAsHeader(): string
    {
        return base64_encode(json_encode(
            self::serialize()
        ));
    }

    /**
     * Get the context serialized, ready to be inserted in a header.
     *
     * @param  string  $header
     * @return void
     */
    public static function restoreFromHeader(string $header): void
    {
        self::unserialize(
            json_decode(base64_decode($header), true)
        );
    }
}
