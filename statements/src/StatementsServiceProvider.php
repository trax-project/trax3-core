<?php

namespace Trax\Statements;

use Trax\Framework\Service\MicroServiceProvider;

class StatementsServiceProvider extends MicroServiceProvider
{
    /**
     * @var string
     */
    protected $serviceKey = 'statements';

    /**
     * @var array
     */
    protected $mixedSingletons = [
        StatementsService::class => [
            'local' => \Trax\Statements\Service::class,
            'remote' => \Trax\Statements\Call\Service::class
        ],
    ];

    /**
     * @var array
     */
    public $singletons = [
        \Trax\Statements\StatementsDatabase::class,
        \Trax\Statements\Repos\Statement\StatementRepository::class,
        \Trax\Statements\Repos\Attachment\AttachmentRepository::class,
        \Trax\Statements\Recording\StatementRecorder::class,
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
