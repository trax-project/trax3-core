<?php

namespace Trax\Framework\Console;

use Illuminate\Console\Command;
use Trax\Framework\Console\Linux;

abstract class WorkersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'something:like {--daemon} {--stop} {--status} {--workers=} {--keep-alive}';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('');

        // Count the running processes.
        if ($this->option('status')) {
            $this->showStatus();
            return;
        }

        // Stop the running processes.
        if ($this->option('stop')) {
            $count = Linux::killProcesses($this->name);

            $this->messageFromCount($count, [
                'There is no worker to stop.' => 'warn',
                '1 worker has been stopped.' => 'info',
                "$count workers have been stopped." => 'info',
            ]);
            return;
        }

        // Start the current process.
        if ($this->option('daemon') || $this->option('workers')) {
            $workers = intval($this->option('workers'));
            $count = $workers ? $workers : 1;

            // Run the command.
            $command = 'php artisan ' . $this->name;
            if ($this->option('keep-alive')) {
                $command .= ' --keep-alive';
            }
            Linux::run($command, $count, false, true);

            $this->messageFromCount($count, [
                'nothing' => 'warn',
                '1 worker has been started.' => 'info',
                "$count workers have been started." => 'info',
            ]);

            $this->showStatus();
            return;
        }

        $this->handleNow();
    }

    /**
     * Show the status.
     *
     * @return void
     */
    protected function showStatus(): void
    {
        $count = Linux::countProcesses($this->name) - 1;

        $this->messageFromCount($count, [
            'There is no worker running!' => 'warn',
            '1 worker is running.' => 'info',
            "$count workers are running." => 'info',
        ]);
    }

    /**
     * Kill the running processes and return the number of killed processes.
     *
     * @param  int  $count
     * @param  array  $outputs
     * @return void
     */
    protected function messageFromCount(int $count, array $outputs): void
    {
        $types = array_values($outputs);
        $messages = array_keys($outputs);
        $pos = $count > 1 ? 2 : $count;
        $type = $types[$pos];
        $message = $messages[$pos];
        $this->$type($message);
        $this->line('');
    }

    /**
     * Run the command.
     *
     * @return void
     */
    abstract protected function handleNow(): void;
}
