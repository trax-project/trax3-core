<?php

namespace Trax\Framework;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Trax\Framework\Http\Validation\ValidationRules;
use Trax\Framework\Xapi\Http\Validation\ValidationRules as XapiValidationRules;
use Trax\Framework\Service\Config;

class FrameworkServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [

        // Services.
        \Trax\Framework\Service\Event\EventManager::class =>
            \Trax\Framework\Service\Event\EventManager::class,

        // Middlewares.
        'trax.context.set' => \Trax\Framework\Http\Middleware\SetContextMiddleware::class,
        'trax.context.restore' => \Trax\Framework\Http\Middleware\RestoreContextMiddleware::class,
        'trax.api' => \Trax\Framework\Http\Middleware\ApiMiddleware::class,
        'trax.cors' => \Trax\Framework\Http\Middleware\CorsMiddleware::class,

        // Exceptions handling.
        'exceptions' => \Trax\Framework\Exceptions\ExceptionHandler::class,

        \Illuminate\Contracts\Debug\ExceptionHandler::class =>
            \Trax\Framework\Exceptions\ExceptionHandler::class,
];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Helpers.
        require_once(__DIR__ . '/../helpers.php');

        // Registrer all the services.
        foreach (Config::services() as $service) {
            $this->app->register(
                Config::serviceProvider($service)
            );
        }

        // Events streamer binding.
        if (class_exists(\Prwnr\Streamer\Stream::class)) {
            // Redis stream.
            $this->app->bind(
                \Trax\Framework\Service\Event\Streamer::class,
                \Trax\Framework\Service\Event\Redis\Streamer::class
            );
        } else {
            // No stream.
            $this->app->bind(
                \Trax\Framework\Service\Event\Streamer::class,
                \Trax\Framework\Service\Event\DefaultStreamer::class
            );
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Define validation rules.
        ValidationRules::register();
        XapiValidationRules::register();

        // Force URLs to HTTPS when the app URL starts with https
        if (Str::of(config('app.url'))->startsWith('https')) {
            app('url')->forceScheme('https');
        }

        // Don't go further if this is not a command.
        if (!$this->app->runningInConsole()) {
            return;
        }

        // Register the commands.
        $this->commands([
            \Trax\Framework\Console\DatabaseInstallCommand::class,
            \Trax\Framework\Console\DatabaseDropCommand::class,
            \Trax\Framework\Console\ServeCommand::class,
            \Trax\Framework\Console\TestCommand::class,
        ]);
        
        // Load all the migrations.
        $this->loadServicesMigrations();
    }

    /**
     * Load all the migration files.
     *
     * @return void
     */
    protected function loadServicesMigrations(): void
    {
        // Framework migrations.
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Services migrations.
        foreach (Config::services() as $service) {
            if (!Config::serviceHasDatabase($service)) {
                continue;
            }
            if (Config::serviceMigrable($service)) {
                $this->loadMigrationsFrom(
                    Config::serviceMigrationsPath($service)
                );
            } else {
                $this->publishMigrationSettings($service);
            }
        }
    }

    /**
     * Publish the migration settings.
     *
     * @param  string  $serviceKey
     * @return void
     */
    protected function publishMigrationSettings(string $serviceKey): void
    {
        $path = Config::servicePath($serviceKey);
        $driver = Config::databaseSettings($serviceKey)->driver;

        $this->publishes([
            base_path("$path/config/$driver.php") => base_path("custom/$driver/$serviceKey.php")
        ], "$driver-config");
    }
}
