<?php

namespace Trax\Framework\Service;

use Illuminate\Support\ServiceProvider;
use Trax\Framework\Service\Config;
use Trax\Framework\Service\Facades\EventManager;

abstract class MicroServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $serviceKey;                  // THIS PROPERY MUST BE DEFINED IN THE CHILD SERVICE PROVIDER.

    /**
     * @var array
     */
    protected $mixedSingletons = [          // THIS PROPERY MUST BE DEFINED IN THE CHILD SERVICE PROVIDER.
        // MicroService::class => ['local' => LocalMicroService::class, 'remote' => RemoteMicroService::class],
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMixedSingletons();
    }

    /**
     * Register the service classes.
     *
     * @return void
     */
    protected function registerMixedSingletons(): void
    {
        $runningLocally = $this->isLocalService();

        foreach ($this->mixedSingletons as $interface => $implementations) {
            $implementation = $runningLocally ? $implementations['local'] : $implementations['remote'];

            $this->app->singleton($interface, function () use ($implementation) {
                return new $implementation($this->app, $this->serviceKey);
            });
        }
    }

    /**
     * Register event listeners.
     * In micro-services, we register only local events.
     * Stream events are registered directly by listening workers.
     *
     * @param  array  $listeners
     * @return void
     */
    protected function registerListeners(array $listeners): void
    {
        foreach ($listeners as $listener) {
            EventManager::registerEventListener((new $listener)->eventClass(), $listener);
        }
    }

    /**
     * Check if the service is running locally.
     *
     * @param  string  $serviceKey
     * @return bool
     */
    protected function isLocalService(string $serviceKey = null): bool
    {
        return isset($serviceKey)
            ? Config::isLocalService($serviceKey)
            : Config::isLocalService($this->serviceKey);
    }

    /**
     * Check if the service is running remotely.
     *
     * @param  string  $serviceKey
     * @return bool
     */
    protected function isRemoteService(string $serviceKey = null): bool
    {
        return !$this->isLocalService($serviceKey);
    }

    /**
     * Check if the service is in dev mode.
     *
     * @return bool
     */
    protected function devMode(): bool
    {
        return Config::devMode();
    }
}
