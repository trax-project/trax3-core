<?php

namespace Trax\ActivityProfiles;

use Trax\Framework\Service\MicroServiceProvider;

class ActivityProfilesServiceProvider extends MicroServiceProvider
{
    /**
     * @var string
     */
    protected $serviceKey = 'activity-profiles';

    /**
     * @var array
     */
    protected $mixedSingletons = [
        ActivityProfilesService::class => [
            'local' => \Trax\ActivityProfiles\Service::class,
            'remote' => \Trax\ActivityProfiles\Call\Service::class
        ],
    ];

    /**
     * @var array
     */
    public $singletons = [
        \Trax\ActivityProfiles\ActivityProfilesDatabase::class,
        \Trax\ActivityProfiles\Repos\ActivityProfile\ActivityProfileRepository::class,
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

        // Don't go further if this is a remote service.
        if (!$this->isLocalService()) {
            return;
        }

        // Don't go further if this is not a command.
        if (!$this->app->runningInConsole()) {
            return;
        }
    }
}
