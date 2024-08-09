<?php

namespace Trax\Starter\Console;

use Symfony\Component\Process\Process;
use Illuminate\Console\Command;
use Trax\Framework\Context;
use Trax\Framework\Auth\ClientRepository;

class TestsuiteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testsuite';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the ADL testsuite';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('');

        // Get the store and client.
        $client = app(ClientRepository::class)->findBySlugOrFail('default');
        $store = $client->store;

        Context::setEntryPoint('starter');
        Context::setStore($store);
        Context::setClient($client);
        Context::setOrigin('console');

        $runner = config('trax.testsuite.path') . '/bin/console_runner.js';
        $username = $client->credentials()->username;
        $password = $client->credentials()->password;
        $endpoint = $client->xapiEndpoint();

        $command = ['node', $runner, '-e', $endpoint, '-a', '-u', $username, '-p', $password, '-x', '1.0.3', '--bail', '--errors'];

        $process = new Process(
            $command,
            null,
            null,
            null,
            60 * 10
        );

        $this->line('');
        $this->info('Launching the ADL LRS conformance testsuite...');
        $this->line('');

        $status = $process->run(function ($type, $data) {
            if ($type == Process::STDOUT) {
                $this->error($data);
            } else {
                $this->line($data);
            }
        });

        $this->line('');

        return $status;
    }
}
