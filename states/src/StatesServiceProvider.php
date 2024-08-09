<?php

namespace Trax\States;

use Trax\Framework\Service\MicroServiceProvider;

class StatesServiceProvider extends MicroServiceProvider
{
    /**
     * @var string
     */
    protected $serviceKey = 'states';

    /**
     * @var array
     */
    protected $mixedSingletons = [
        StatesService::class => [
            'local' => \Trax\States\Service::class,
            'remote' => \Trax\States\Call\Service::class
        ],
    ];

    /**
     * @var array
     */
    public $singletons = [
        \Trax\States\StatesDatabase::class,
        \Trax\States\Repos\State\StateRepository::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Define routes.
        if ($this->app->runningUnitTests() || ($this->isLocalService() && !$this->isLocalService('gateway'))) {
            $this->loadRoutesFrom(__DIR__.'/../routes/service.php');
        }
    }
}
