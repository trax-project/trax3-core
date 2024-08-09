<?php

namespace Trax\Starter;

use Illuminate\Support\Facades\Auth;
use Trax\Framework\Service\MicroServiceProvider;
use Trax\Framework\Auth\Env\EnvUserProvider;
use Trax\Framework\Service\Config;

class StarterServiceProvider extends MicroServiceProvider
{
    /**
     * @var string
     */
    protected $serviceKey = 'starter';

    /**
     * @var array
     */
    protected $mixedSingletons = [
        StarterService::class => [
            'local' => \Trax\Starter\Service::class,
            'remote' => \Trax\Starter\Call\Service::class
        ],
    ];

    /**
     * @var array
     */
    public $singletons = [
        \Trax\Framework\Auth\Authorizer::class => \Trax\Framework\Auth\Env\EnvAuthorizer::class,
        \Trax\Framework\Auth\ClientRepository::class => \Trax\Framework\Auth\Env\EnvClientRepository::class,
        \Trax\Framework\Auth\UserRepository::class => \Trax\Framework\Auth\Env\EnvUserRepository::class,
        \Trax\Framework\Auth\StoreRepository::class => \Trax\Framework\Auth\Env\EnvStoreRepository::class,
        \Trax\Framework\Logging\LoggerContract::class => \Trax\Framework\Logging\BasicLogger::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Define routes.
        if ($this->app->runningUnitTests() || $this->isLocalService()) {
            $this->loadRoutesFrom(__DIR__.'/../routes/service.php');
        }

        // Don't go further if this is a remote service.
        if (!$this->isLocalService()) {
            return;
        }
        
        // Define the auth user providers.
        Auth::provider('env', function () {
            return new EnvUserProvider();
        });

        // Load views.
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'trax-starter');

        // Commands.
        // These are alternative commands to the Extended Edition.
        $this->commands([
            \Trax\Starter\Console\TestsuiteCommand::class,
            \Trax\Starter\Console\PublishCommand::class,
        ]);
    }
}
