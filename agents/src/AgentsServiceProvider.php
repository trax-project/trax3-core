<?php

namespace Trax\Agents;

use Trax\Framework\Service\MicroServiceProvider;

class AgentsServiceProvider extends MicroServiceProvider
{
    /**
     * @var string
     */
    protected $serviceKey = 'agents';

    /**
     * @var array
     */
    protected $mixedSingletons = [
        AgentsService::class => [
            'local' => \Trax\Agents\Service::class,
            'remote' => \Trax\Agents\Call\Service::class
        ],
    ];

    /**
     * @var array
     */
    public $singletons = [
        \Trax\Agents\AgentsDatabase::class,
        \Trax\Agents\Repos\Agent\AgentRepository::class,
        \Trax\Agents\Recording\AgentUpdater::class,
        \Trax\Agents\Recording\AgentRecorder::class,
        \Trax\Agents\Recording\PersonRecorder::class,
    ];

    /**
     * List of commands.
     *
     * @var array
     */
    protected $commands = [
        \Trax\Agents\Console\UpdateAgentsCommand::class,
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
            \Trax\Agents\Listeners\StatementsRecordedEventListener::class,
        ]);

        // Don't go further if this is not a command.
        if (!$this->app->runningInConsole()) {
            return;
        }

        // Registrer the commands.
        $this->commands($this->commands);
    }
}
