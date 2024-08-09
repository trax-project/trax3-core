<?php

namespace Trax\Framework\Console;

use Illuminate\Foundation\Console\ServeCommand as LaravelServeCommand;

class ServeCommand extends LaravelServeCommand
{
    /**
     * We name this command `dev:serve` to avoid any confusion with the native command `serve`.
     * However, we must keep this command in the framework because all services need it, not only the `dev` service.
     *
     * @var string
     */
    protected $name = 'dev:serve';

    /**
     * The environment variables that should be passed from host machine to the PHP server process.
     *
     * @var string[]
     */
    public static $passthroughVariables = [
        'APP_ENV',
        'HERD_PHP_81_INI_SCAN_DIR',
        'HERD_PHP_82_INI_SCAN_DIR',
        'HERD_PHP_83_INI_SCAN_DIR',
        'IGNITION_LOCAL_SITES_PATH',
        'LARAVEL_SAIL',
        'PATH',
        'PHP_CLI_SERVER_WORKERS',
        'PHP_IDE_CONFIG',
        'SYSTEMROOT',
        'XDEBUG_CONFIG',
        'XDEBUG_MODE',
        'XDEBUG_SESSION',

        'LOCAL_SERVICES',
        'REMOTE_SERVICES',

        'SERVICE_AUTH_HOST',
        'SERVICE_COMMANDS_HOST',
        'SERVICE_DEV_HOST',
        'SERVICE_GATEWAY_HOST',
        'SERVICE_LOGGING_HOST',
        
        'SERVICE_ACTIVITIES_HOST',
        'SERVICE_ACTIVITY_PROFILES_HOST',
        'SERVICE_AGENTS_HOST',
        'SERVICE_AGENT_PROFILES_HOST',
        'SERVICE_STATEMENTS_HOST',
        'SERVICE_STATES_HOST',
        'SERVICE_VOCAB_HOST',

        'SERVICE_STARTER_HOST',
        'SERVICE_EXTENDED_HOST',
    ];
}
