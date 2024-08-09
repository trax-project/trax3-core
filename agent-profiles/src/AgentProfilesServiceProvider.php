<?php

namespace Trax\AgentProfiles;

use Trax\Framework\Service\MicroServiceProvider;

class AgentProfilesServiceProvider extends MicroServiceProvider
{
    /**
     * @var string
     */
    protected $serviceKey = 'agent-profiles';

    /**
     * @var array
     */
    protected $mixedSingletons = [
        AgentProfilesService::class => [
            'local' => \Trax\AgentProfiles\Service::class,
            'remote' => \Trax\AgentProfiles\Call\Service::class
        ],
    ];

    /**
     * @var array
     */
    public $singletons = [
        \Trax\AgentProfiles\AgentProfilesDatabase::class,
        \Trax\AgentProfiles\Repos\AgentProfile\AgentProfileRepository::class,
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
