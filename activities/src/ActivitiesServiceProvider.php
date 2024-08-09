<?php

namespace Trax\Activities;

use Trax\Framework\Service\MicroServiceProvider;

class ActivitiesServiceProvider extends MicroServiceProvider
{
    /**
     * @var string
     */
    protected $serviceKey = 'activities';

    /**
     * @var array
     */
    protected $mixedSingletons = [
        ActivitiesService::class => [
            'local' => \Trax\Activities\Service::class,
            'remote' => \Trax\Activities\Call\Service::class
        ],
    ];

    /**
     * @var array
     */
    public $singletons = [
        \Trax\Activities\ActivitiesDatabase::class,
        \Trax\Activities\Repos\Activity\ActivityRepository::class,
        \Trax\Activities\Recording\ActivityUpdater::class,
        \Trax\Activities\Recording\ActivityRecorder::class,
    ];

    /**
     * List of commands.
     *
     * @var array
     */
    protected $commands = [
        \Trax\Activities\Console\UpdateActivitiesCommand::class,
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
        
        // Register event listeners.
        $this->registerListeners([
            \Trax\Activities\Listeners\StatementsRecordedEventListener::class,
        ]);

        // Don't go further if this is not a command.
        if (!$this->app->runningInConsole()) {
            return;
        }

        // Registrer the commands.
        $this->commands($this->commands);
    }
}
