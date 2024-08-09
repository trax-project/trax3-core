<?php

namespace Trax\Starter\Console;

use Symfony\Component\Process\Process;
use Illuminate\Console\Command;
use Trax\Framework\Context;
use Trax\Framework\Auth\ClientRepository;

use Illuminate\Support\Facades\File;


class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the build assets of the Starter Edition';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('');

        if (!File::exists(base_path('trax/core/starter/build'))) {
            $this->error('The assets folder of the Starter Edition does not exist.');
            $this->line('');
            return;
        }

        File::deleteDirectory(public_path('starter'));
        $this->info('Former assets folder deleted');

        File::copyDirectory(base_path('trax/core/starter/build'), public_path('starter'));
        $this->info('New assets folder copyed');

        $this->line('');
    }
}
