<?php

namespace Trax\Framework\Console;

use Symfony\Component\Process\Process;

class Linux
{
    /**
     * Count the running processes.
     *
     * @param  string  $name
     * @return int
     *
     * @throws \Exception
     */
    public static function countProcesses(string $name): int
    {
        $process = new Process(['pgrep', '-f', "artisan $name"]);
        $process->run();

        if (!empty($process->getErrorOutput())) {
            throw new \Exception($process->getErrorOutput());
        }

        return count(explode("\n", $process->getOutput())) - 1;
    }

    /**
     * Kill the running processes and return the number of killed processes.
     *
     * @param  string  $name
     * @return int
     *
     * @throws \Exception
     */
    public static function killProcesses(string $name): int
    {
        if (!$count = self::countProcesses($name)) {
            return 0;
        }

        $process = new Process(['pkill', '-f', "artisan $name"]);
        $process->run();

        // This would be better but it does not work.
        // self::run("pkill -f artisan $name", 1, false);

        return $count;
    }

    /**
     * Kill the running processes and return the number of killed processes.
     *
     * @param  string  $command
     * @param  int  $count
     * @return void
     */
    public static function run(string $command, int $count = 1, bool $showFeedbacks = true, bool $runInBackground = false)
    {
        if (!$showFeedbacks) {
            $command .= ' >> /dev/null 2>&1';
        }

        if ($runInBackground) {
            $command .= ' &';
        }

        $process = Process::fromShellCommandline($command);

        for ($i = 0; $i < $count; $i++) {
            $process->run();
        }
    }
}
