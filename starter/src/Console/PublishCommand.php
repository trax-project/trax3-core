<?php

namespace Trax\Starter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Trax\Framework\Service\Config;

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

        $buildPath = Config::servicePath('starter') . '/build';

        if (!File::exists($buildPath)) {
            $this->error('The assets folder of the Starter Edition does not exist.');
            $this->line('');
            return;
        }

        File::deleteDirectory(public_path('starter'));
        $this->info('Former assets folder deleted');

        File::copyDirectory($buildPath, public_path('starter'));
        $this->info('New assets folder copyed');

        $this->line('');
    }
}
