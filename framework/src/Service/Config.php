<?php
 
namespace Trax\Framework\Service;

use Trax\Framework\Context;
use Trax\Framework\Auth\StoreRepository;

class Config
{
    /**
     * Are async mode for some databases such as Elasticsearch enaled?
     *
     * @var bool
     */
    protected static $asyncRequestsEnabled = true;

    /**
     * Are async mode for some databases such as Elasticsearch enaled?
     *
     * @return bool
     */
    public static function asyncRequestsEnabled(): bool
    {
        return self::$asyncRequestsEnabled;
    }

    /**
     * Disable async mode for some databases such as Elasticsearch.
     *
     * @return void
     */
    public static function disableAsyncRequests(): void
    {
        self::$asyncRequestsEnabled = false;
    }

    /**
     * Return the default xAPI endpoint URL.
     *
     * @return string
     */
    public static function defaultXapiEndpoint(): string
    {
        return traxClientStoreUrl('default', 'default', true) . '/xapi';
    }

    /**
     * Return the stores data.
     *
     * @return array
     */
    public static function storesData(): array
    {
        $readyStores = app(StoreRepository::class)->getReady();

        if ($readyStores->isEmpty()) {
            return ['stores' => [
                'default' => null,
                'choice' => [],
            ]];
        }

        $default = $readyStores->first()->slug;

        $choice = $readyStores->map(function ($store) {
            return ['id' => $store->slug, 'name' => $store->name];
        });
        
        return ['stores' => [
            'default' => $default,
            'choice' => $choice,
        ]];
    }

    /**
     * Return the users data.
     *
     * @return array
     */
    public static function authData(): array
    {
        return ['auth' => Context::user()];
    }

    /**
     * Return the app config.
     *
     * @return array
     */
    public static function appSettings(): array
    {
        $connection = config('database.default');

        return ['settings' => [
            // App.
            'APP_ENV' => config('app.env'),
            'APP_DEBUG' => config('app.debug'),
            'APP_URL' => config('app.url'),
            
            // Main database.
            'DB_CONNECTION' => $connection,
            'DB_HOST' => config("database.connections.$connection.host"),
            'DB_PORT' => config("database.connections.$connection.port"),
            'DB_DATABASE' => config("database.connections.$connection.database"),
            'DB_SCHEMA' => config("database.connections.$connection.search_path"),

            // xAPI main settings.
            // Don't remove it from here. Starter edition needs it.
            'XAPI_STATEMENTS_LIMIT' => config('trax.xapi.statements_limit'),
            'XAPI_STATEMENTS_AUTHORITY_NAME' => config('trax.xapi.authority.account.name'),
            'XAPI_STATEMENTS_AUTHORITY_HOMEPAGE' => config('trax.xapi.authority.account.homePage'),
            'XAPI_PSEUDO_HOMEPAGE' => config('trax.xapi.pseudo.homepage'),
            'XAPI_PSEUDO_HASH_KEY' => '********',
        ]];
    }

    /**
     * Return the xAPI config.
     *
     * @return array
     */
    public static function xapiSettings(): array
    {
        return ['settings' => [
            // Main settings.
            'XAPI_STATEMENTS_LIMIT' => config('trax.xapi.statements_limit'),
            'XAPI_STATEMENTS_AUTHORITY_NAME' => config('trax.xapi.authority.account.name'),
            'XAPI_STATEMENTS_AUTHORITY_HOMEPAGE' => config('trax.xapi.authority.account.homePage'),
            'XAPI_PSEUDO_HOMEPAGE' => config('trax.xapi.pseudo.homepage'),
            'XAPI_PSEUDO_HASH_KEY' => '********',

            // xAPI pipeline.
            'PIPELINE_DEFAULT_VALIDATE_STATEMENTS' => config('trax.pipeline.validate_statements.default'),
            'PIPELINE_DEFAULT_CHECK_CONFLICTS' => config('trax.pipeline.check_conflicts.default'),
            'PIPELINE_DEFAULT_RECORD_ATTACHMENTS' => config('trax.pipeline.record_attachments.default'),
            'PIPELINE_DEFAULT_VOID_STATEMENTS' => config('trax.pipeline.void_statements.default'),
            'PIPELINE_DEFAULT_UPDATE_ACTIVITIES' => config('trax.pipeline.update_activities.default'),
            'PIPELINE_DEFAULT_UPDATE_AGENTS' => config('trax.pipeline.update_agents.default'),
            'PIPELINE_DEFAULT_UPDATE_VOCAB' => config('trax.pipeline.update_vocab.default'),
            'PIPELINE_DEFAULT_UPDATE_ACTIVITY_IDS' => config('trax.pipeline.update_activity_ids.default'),
            'PIPELINE_DEFAULT_UPDATE_AGENT_IDS' => config('trax.pipeline.update_agent_ids.default'),
            'PIPELINE_DEFAULT_QUERY_TARGETING' => config('trax.pipeline.query_targeting.default'),

            // xAPI pipeline forced.
            'PIPELINE_FORCED_VALIDATE_STATEMENTS' => config('trax.pipeline.validate_statements.forced'),
            'PIPELINE_FORCED_CHECK_CONFLICTS' => config('trax.pipeline.check_conflicts.forced'),
            'PIPELINE_FORCED_RECORD_ATTACHMENTS' => config('trax.pipeline.record_attachments.forced'),
            'PIPELINE_FORCED_VOID_STATEMENTS' => config('trax.pipeline.void_statements.forced'),
            'PIPELINE_FORCED_UPDATE_ACTIVITIES' => config('trax.pipeline.update_activities.forced'),
            'PIPELINE_FORCED_UPDATE_AGENTS' => config('trax.pipeline.update_agents.forced'),
            'PIPELINE_FORCED_UPDATE_VOCAB' => config('trax.pipeline.update_vocab.forced'),
            'PIPELINE_FORCED_UPDATE_ACTIVITY_IDS' => config('trax.pipeline.update_activity_ids.forced'),
            'PIPELINE_FORCED_UPDATE_AGENT_IDS' => config('trax.pipeline.update_agent_ids.forced'),
            'PIPELINE_FORCED_QUERY_TARGETING' => config('trax.pipeline.query_targeting.forced'),

            // Testsuite.
            'TESTSUITE_PATH' => config('trax.testsuite.path'),
        ]];
    }

    /**
     * Return the access config.
     *
     * @return array
     */
    public static function accessSettings(): array
    {
        return ['settings' => [
            // Super-admin.
            'ADMIN_EMAIL' => config('trax.auth.admin.email'),

            // Default xAPI endpoint URL.
            'DEFAULT_ENDPOINT_URL' => self::defaultXapiEndpoint(),
            'DEFAULT_ENDPOINT_USERNAME' => config('trax.auth.endpoint.username'),
        ]];
    }

    /**
     * Return the statements service config.
     *
     * @return array
     */
    public static function statementsServiceSettings(): array
    {
        return ['settings' => [
            // Database.
            'STATEMENTS_DB_DRIVER' => config("database.connections.statements.driver"),
            'STATEMENTS_DB_HOST' => config("database.connections.statements.host"),
            'STATEMENTS_DB_PORT' => config("database.connections.statements.port"),
            'STATEMENTS_DB_DATABASE' => config("database.connections.statements.database"),
            'STATEMENTS_DB_SCHEMA' => config("database.connections.statements.schema"),
            'STATEMENTS_DB_ASYNC' => config("database.connections.statements.async"),
            'STATEMENTS_DB_TIMESERIES' => config("database.connections.statements.timeseries"),
        ]];
    }

    /**
     * Return the activities service config.
     *
     * @return array
     */
    public static function activitiesServiceSettings(): array
    {
        return ['settings' => [
            // Database.
            'ACTIVITIES_DB_DRIVER' => config("database.connections.activities.driver"),
            'ACTIVITIES_DB_HOST' => config("database.connections.activities.host"),
            'ACTIVITIES_DB_PORT' => config("database.connections.activities.port"),
            'ACTIVITIES_DB_DATABASE' => config("database.connections.activities.database"),
            'ACTIVITIES_DB_SCHEMA' => config("database.connections.activities.schema"),
            'ACTIVITIES_DB_ASYNC' => config("database.connections.activities.async"),
        ]];
    }

    /**
     * Return the agents service config.
     *
     * @return array
     */
    public static function agentsServiceSettings(): array
    {
        return ['settings' => [
            // Database.
            'AGENTS_DB_DRIVER' => config("database.connections.agents.driver"),
            'AGENTS_DB_HOST' => config("database.connections.agents.host"),
            'AGENTS_DB_PORT' => config("database.connections.agents.port"),
            'AGENTS_DB_DATABASE' => config("database.connections.agents.database"),
            'AGENTS_DB_SCHEMA' => config("database.connections.agents.schema"),
            'AGENTS_DB_ASYNC' => config("database.connections.agents.async"),
        ]];
    }

    /**
     * Return the activity-profiles service config.
     *
     * @return array
     */
    public static function activityProfilesServiceSettings(): array
    {
        return ['settings' => [
            // Database.
            'ACTIVITY_PROFILES_DB_DRIVER' => config("database.connections.activity-profiles.driver"),
            'ACTIVITY_PROFILES_DB_HOST' => config("database.connections.activity-profiles.host"),
            'ACTIVITY_PROFILES_DB_PORT' => config("database.connections.activity-profiles.port"),
            'ACTIVITY_PROFILES_DB_DATABASE' => config("database.connections.activity-profiles.database"),
            'ACTIVITY_PROFILES_DB_SCHEMA' => config("database.connections.activity-profiles.schema"),
            'ACTIVITY_PROFILES_DB_ASYNC' => config("database.connections.activity-profiles.async"),
        ]];
    }

    /**
     * Return the agent-profiles service config.
     *
     * @return array
     */
    public static function agentProfilesServiceSettings(): array
    {
        return ['settings' => [
            // Database.
            'AGENT_PROFILES_DB_DRIVER' => config("database.connections.agent-profiles.driver"),
            'AGENT_PROFILES_DB_HOST' => config("database.connections.agent-profiles.host"),
            'AGENT_PROFILES_DB_PORT' => config("database.connections.agent-profiles.port"),
            'AGENT_PROFILES_DB_DATABASE' => config("database.connections.agent-profiles.database"),
            'AGENT_PROFILES_DB_SCHEMA' => config("database.connections.agent-profiles.schema"),
            'AGENT_PROFILES_DB_ASYNC' => config("database.connections.agent-profiles.async"),
        ]];
    }

    /**
     * Return the states service config.
     *
     * @return array
     */
    public static function statesServiceSettings(): array
    {
        return ['settings' => [
            // Database.
            'STATES_DB_DRIVER' => config("database.connections.states.driver"),
            'STATES_DB_HOST' => config("database.connections.states.host"),
            'STATES_DB_PORT' => config("database.connections.states.port"),
            'STATES_DB_DATABASE' => config("database.connections.states.database"),
            'STATES_DB_SCHEMA' => config("database.connections.states.schema"),
            'STATES_DB_ASYNC' => config("database.connections.states.async"),
        ]];
    }

    /**
     * Return the logging service config.
     *
     * @return array
     */
    public static function loggingServiceSettings(): array
    {
        return ['settings' => [
            // Database.
            'LOGGING_DB_DRIVER' => config("database.connections.logging.driver"),
            'LOGGING_DB_HOST' => config("database.connections.logging.host"),
            'LOGGING_DB_PORT' => config("database.connections.logging.port"),
            'LOGGING_DB_DATABASE' => config("database.connections.logging.database"),
            'LOGGING_DB_SCHEMA' => config("database.connections.logging.schema"),
            'LOGGING_DB_ASYNC' => config("database.connections.logging.async"),
        ]];
    }

    /**
     * Return the vocab service config.
     *
     * @return array
     */
    public static function vocabServiceSettings(): array
    {
        return ['settings' => [
            // Database.
            'VOCAB_DB_DRIVER' => config("database.connections.vocab.driver"),
            'VOCAB_DB_HOST' => config("database.connections.vocab.host"),
            'VOCAB_DB_PORT' => config("database.connections.vocab.port"),
            'VOCAB_DB_DATABASE' => config("database.connections.vocab.database"),
            'VOCAB_DB_SCHEMA' => config("database.connections.vocab.schema"),
            'VOCAB_DB_ASYNC' => config("database.connections.vocab.async"),
        ]];
    }

    /**
     * Return the names of services.
     *
     * @return array
     */
    public static function services(): array
    {
        return array_filter(array_keys(
            config("trax.services")
        ), function ($service) {
            if ($service === 'starter' && self::extendedEdition()) {
                return false;
            }
            if ($service === 'extended' && !self::extendedEdition()) {
                return false;
            }
            return true;
        });
    }

    /**
     * Return the names of xAPI services.
     *
     * @return array
     */
    public static function xapiServices(): array
    {
        return ['statements', 'activities', 'agents', 'activity-profiles', 'agent-profiles', 'states'];
    }

    /**
     * Return the names of storable services.
     *
     * @return array
     */
    public static function storableServices(): array
    {
        return collect(self::services())->filter(function ($service) {
            $database = self::database($service);
            return !empty($database) && $database->isStorable();
        })->all();
    }

    /**
     * Return the names of storable services which support NoSQL drivers.
     *
     * @return array
     */
    public static function nosqlStorableServices(): array
    {
        return collect(self::services())->filter(function ($service) {
            $database = self::database($service);
            return !empty($database) && $database->isStorable() && $database->nosqlSupported();
        })->all();
    }

    /**
     * Return the service provider.
     *
     * @param  string  $service
     * @return string
     */
    public static function serviceProvider(string $service)
    {
        return self::serviceConfig($service)['service'] . 'Provider';
    }

    /**
     * Return the service class.
     *
     * @param  string  $service
     * @return string
     */
    public static function serviceClass(string $service)
    {
        return self::serviceConfig($service)['service'];
    }

    /**
     * Return the service host.
     *
     * @param  string  $service
     * @return string
     */
    public static function serviceHost(string $service)
    {
        return self::serviceConfig($service)['host'];
    }

    /**
     * Return the service path.
     *
     * @param  string  $service
     * @return string
     */
    public static function servicePath(string $service)
    {
        return self::serviceConfig($service)['path'];
    }

    /**
     * Tell if the given service has a database.
     *
     * @param  string  $service
     * @return bool
     */
    public static function serviceHasDatabase(string $service)
    {
        return !empty(
            self::databaseClass($service)
        );
    }

    /**
     * Provides the path to the migrations folder.
     *
     * @param  string  $service
     * @return string|null
     */
    public static function serviceMigrationsPath(string $service)
    {
        if (!self::serviceHasDatabase($service)) {
            return null;
        }
        return base_path(
            self::servicePath($service) . '/database/migrations'
        );
    }

    /**
     * Tell if the given service has a listener.
     *
     * @param  string  $service
     * @return bool
     */
    public static function serviceHasListeners(string $service)
    {
        return !empty(
            self::serviceConfig($service)['listeners']
        );
    }

    /**
     * Return a service config.
     *
     * @param  string  $name
     * @param  string  $prop
     * @param  string  $default
     * @return mixed
     */
    protected static function serviceConfig(string $name, string $prop = null, $default = null): mixed
    {
        if (isset($prop)) {
            return config("trax.services.$name.$prop", $default);
        }
        return config("trax.services.$name");
    }

    /**
     * Return the settings of a database given a connection.
     *
     * @param  string  $connection
     * @return object|null
     */
    public static function databaseSettings(string $connection)
    {
        return (object) config("database.connections.$connection");
    }

    /**
     * Return the DB manager class of a service.
     *
     * @param  string  $service
     * @return string|null
     */
    public static function databaseClass(string $service)
    {
        return config("trax.services.$service.database", null);
    }

    /**
     * Return the DB manager of a service.
     *
     * @param  string  $service
     * @return \Trax\Framework\Database\Database|null
     */
    public static function database(string $service)
    {
        $class = self::databaseClass($service);

        return is_null($class) ? null : app($class);
    }

    /**
     * Return the list of DB tables of a given service.
     *
     * @param  string  $service
     * @return array
     */
    public static function databaseTables(string $service): array
    {
        if (!$database = self::databaseClass($service)) {
            return [];
        }

        return $database::tables();
    }

    /**
     * Return the DB connection of a given service.
     *
     * @param  string  $service
     * @return string|null
     */
    public static function databaseConnection(string $service)
    {
        if (!$database = self::databaseClass($service)) {
            return null;
        }
        return $database::connection();
    }

    /**
     * Return the list of DB connections.
     *
     * @param  bool  $includeDefault
     * @return array
     */
    public static function databaseConnections(bool $includeDefault = true): array
    {
        return collect(self::services())->map(function ($service) {
            return self::databaseConnection($service);
        })->filter(function ($connection) use ($includeDefault) {
            return !empty($connection) && (
                $includeDefault || $connection != config('database.default')
            );
        })->all();
    }

    /**
     * Check if the services are all local.
     *
     * @param  array  $serviceKeys
     * @return bool
     */
    public static function areLocalServices(array $serviceKeys): bool
    {
        $local = true;
        foreach ($serviceKeys as $key) {
            $local = $local && self::isLocalService($key);
        }
        return $local;
    }

    /**
     * Check if the service is local.
     *
     * @param  string  $serviceKey
     * @return bool
     */
    public static function isLocalService(string $serviceKey): bool
    {
        $local = config('trax.deployment.local_services');
        if ($local == 'all') {
            return true;
        }
        if (is_string($local)) {
            $local = json_decode($local);
        }
        if ($local === false) {
            throw new \Exception("Config 'trax.deployment.local_services' must have the 'all' value or be an array!");
        }
        return in_array($serviceKey, $local);
    }

    /**
     * Check if the service is remote.
     *
     * @param  string  $serviceKey
     * @return bool
     */
    public static function isRemoteService(string $serviceKey): bool
    {
        $remote = config('trax.deployment.remote_services');
        if ($remote == 'all') {
            return true;
        }
        if (is_string($remote)) {
            $remote = json_decode($remote);
        }
        if ($remote === false) {
            throw new \Exception("Config 'trax.deployment.remote_services' must have the 'all' value or be an array!");
        }
        return in_array($serviceKey, $remote);
    }

    /**
     * Check if the service is disabled.
     *
     * @param  string  $serviceKey
     * @return bool
     */
    public static function serviceDisabled(string $serviceKey): bool
    {
        return !self::isLocalService($serviceKey) && !self::isRemoteService($serviceKey);
    }

    /**
     * Check if the service manages migrations.
     *
     * @param  string  $serviceKey
     * @return bool
     */
    public static function serviceMigrable(string $serviceKey): bool
    {
        // No specific connection. It is migrable (default).
        if (!isset(config('database.connections')[$serviceKey])) {
            return true;
        }

        // Specific connection. Check the driver.
        return in_array(
            self::databaseSettings($serviceKey)->driver,
            ['mysql', 'pgsql']
        );
    }

    /**
     * Check if we are in dev mode.
     *
     * @return bool
     */
    public static function devMode(): bool
    {
        return config('app.env') == 'local' || config('app.env') == 'testing';
    }

    /**
     * Check if the event stream is enabled.
     *
     * @return bool
     */
    public static function streamEnabled(): bool
    {
        return config('trax.deployment.event_stream.enabled');
    }

    /**
     * Check if the extended API is enabled.
     *
     * @return bool
     */
    public static function extendedEdition(): bool
    {
        return config('trax.features.extended-edition');
    }

    /**
     * Check if the API is opend.
     *
     * @return bool
     */
    public static function dataApi(): bool
    {
        return config('trax.features.data-api');
    }

    /**
     * Check if the API is opend.
     *
     * @return bool
     */
    public static function jobsApi(): bool
    {
        return config('trax.features.jobs-api');
    }

    /**
     * Check if the API is opend.
     *
     * @return bool
     */
    public static function loggingApi(): bool
    {
        return config('trax.features.logging-api');
    }

    /**
     * Check if the API is opend.
     *
     * @return bool
     */
    public static function accessApi(): bool
    {
        return config('trax.features.access-api');
    }
}
