<?php

namespace Trax\Framework\Console;

use Illuminate\Console\Command;
use Trax\Framework\Service\Config;

class DatabaseDropCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:drop {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all the databases';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->line('');
        
        if (!$this->option('force') && !$this->confirm("Are you sure you want to drop all the databases?")) {
            return;
        }

        foreach (Config::databaseConnections() as $connection) {
            $driver = config('database.connections.' . $connection . '.driver');

            if ($driver == 'mongodb') {
                // Don't declare the class at the begining of the file.
                // This is an extension and we don't know if it is installed.
                (new \Trax\Extensions\Repo\Mongo\MongoDatabase($connection))->drop();
            
            } elseif ($driver == 'elasticsearch') {
                // Don't declare the class at the begining of the file.
                // This is an extension and we don't know if it is installed.
                (new \Trax\Extensions\Repo\Elastic\ElasticDatabase($connection))->drop();
            
            } elseif ($driver == 'opensearch') {
                // Don't declare the class at the begining of the file.
                // This is an extension and we don't know if it is installed.
                (new \Trax\Extensions\Repo\OpenSearch\OpenSearchDatabase($connection))->drop();
            
            } else {
                $this->call('db:wipe', array_filter(['--database' => $connection, '--force' => true]));
            }
        }
    }
}
