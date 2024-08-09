<?php

namespace Trax\Gateway;

use Trax\Framework\Service\MicroServiceProvider;

class GatewayServiceProvider extends MicroServiceProvider
{
    /**
     * @var string
     */
    protected $serviceKey = 'gateway';

    /**
     * @var array
     */
    protected $mixedSingletons = [
        GatewayService::class => [
            'local' => \Trax\Gateway\Service::class,
            'remote' => \Trax\Gateway\Call\Service::class
        ],
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningUnitTests() || $this->isLocalService()) {
            $this->loadRoutesFrom(__DIR__.'/../routes/service.php');
        }
    }
}
