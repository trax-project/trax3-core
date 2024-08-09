<?php

namespace Trax\Framework\Console;

use Illuminate\Console\Command;
use Trax\Framework\Service\Config;
use Trax\Framework\Auth\StoreRepository;
use Trax\Framework\Auth\ClientRepository;
use Trax\Framework\Auth\UserRepository;
use Trax\Framework\Xapi\Helpers\XapiPipeline;

class DatabaseInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:install {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate all the databases';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Drop all the databases.
        if (!$this->option('force') && !$this->confirm("Are you sure you want to drop all the databases?")) {
            return;
        }
        $this->call('database:drop', ['--force' => true]);

        // Run the migration.
        $this->call('migrate', ['--force' => true]);

        // Databases declared with an independent driver supporting NoSQL.
        foreach (Config::databaseConnections() as $connection) {
            $driver = config('database.connections.' . $connection . '.driver');

            if ($driver == 'mongodb') {
                $this->migrateMongo($connection);
            }
    
            if ($driver == 'elasticsearch') {
                $this->migrateElastic($connection);
            }
    
            if ($driver == 'opensearch') {
                $this->migrateOpenSearch($connection);
            }
        }

        // Create the default store.
        app(StoreRepository::class)->create([
            'slug' => 'default',
            'name' => 'Default store',
        ]);
        $this->info('Default store created.');

        // Create the default client.
        app(ClientRepository::class)->create([
            'slug' => 'default',
            'name' => 'Default client',
            'guard' => 'basic-http',
            'credentials' => [
                'username' => config('trax.auth.endpoint.username'),
                'password' => config('trax.auth.endpoint.password'),
            ],
            'cors' => '*',
            'permissions' => [
                'xapi/all' => true,
                'cmi5/tokens' => true,
                'data/all' => false,
                'tasks/all' => false,
                'logging/all' => false,
                'access/all' => false,
            ],
            'pipeline' => (new XapiPipeline)->serialize(),
            'store' => 'default',
        ]);
        $this->info('Default client created.');

        // Create the admin user.
        app(UserRepository::class)->create([
            'firstname' => 'Super',
            'lastname' => 'Admin',
            'email' => config('trax.auth.admin.email'),
            'password' => config('trax.auth.admin.password'),
            'agent' => null,
            'role' => 'admin',
        ]);
        $this->info('Admin user created.');

        $this->line('');
    }

    /**
     * Migrate the MongoDB database.
     *
     * @param  string  $connection
     * @return void
     */
    protected function migrateMongo(string $connection)
    {
        // Don't declare the class at the begining of the file.
        // This is an extension and we don't know if it is installed.
        $database = new \Trax\Extensions\Repo\Mongo\MongoDatabase($connection);

        // Then, build the indexes.
        $database->createIndexes('default');

        $this->info($connection . ': MongoDB indexes created.');
    }

    /**
     * Migrate the Elasticsearch database.
     *
     * @param  string  $connection
     * @return void
     */
    protected function migrateElastic(string $connection)
    {
        // Don't declare the class at the begining of the file.
        // This is an extension and we don't know if it is installed.
        $database = new \Trax\Extensions\Repo\Elastic\ElasticDatabase($connection);

        $database->createIndexes('default');

        $this->info($connection . ': Elasticsearch indexes created.');
    }

    /**
     * Migrate the OpenSearch database.
     *
     * @param  string  $connection
     * @return void
     */
    protected function migrateOpenSearch(string $connection)
    {
        // Don't declare the class at the begining of the file.
        // This is an extension and we don't know if it is installed.
        $database = new \Trax\Extensions\Repo\OpenSearch\OpenSearchDatabase($connection);

        $database->createIndexes('default');

        $this->info($connection . ': OpenSearch indexes created.');
    }
}
