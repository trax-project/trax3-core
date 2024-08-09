<?php

namespace Trax\Agents\Console;

use Trax\Framework\Console\ListenCommand as BaseListenCommand;

class UpdateAgentsCommand extends BaseListenCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agents:update {--daemon} {--stop} {--status} {--workers=} {--keep-alive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the agents updater';

    /**
     * The streams listeners.
     *
     * @var array
     */
    protected $listeners = [
        'statements-store' => [
            \Trax\Agents\Listeners\StatementsRecordedStreamListener::class,
        ]
    ];

    /**
     * Return the name of the service.
     *
     * @return string
     */
    protected function service(): string
    {
        return 'agent';
    }
}
