<?php

namespace Trax\Activities\Console;

use Trax\Framework\Console\ListenCommand as BaseListenCommand;

class UpdateActivitiesCommand extends BaseListenCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activities:update {--daemon} {--stop} {--status} {--workers=} {--keep-alive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the activities updater';

    /**
     * The streams listeners.
     *
     * @var array
     */
    protected $listeners = [
        'statements-store' => [
            \Trax\Activities\Listeners\StatementsRecordedStreamListener::class,
        ]
    ];

    /**
     * Return the name of the service.
     *
     * @return string
     */
    protected function service(): string
    {
        return 'activities';
    }
}
