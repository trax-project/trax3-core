<?php

namespace Trax\Framework\Tests\Utils;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase as LaravelRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Trax\Framework\Service\Config;

trait RefreshDatabase
{
    use LaravelRefreshDatabase;

    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function resetDatabase()
    {
        RefreshDatabaseState::$migrated = false;
        $this->refreshTestDatabase();
    }

    /**
     * Refresh a conventional test database.
     *
     * @return void
     */
    protected function refreshTestDatabase()
    {
        if (! RefreshDatabaseState::$migrated) {
            // Use our own command: php artisan `database:install` if it is available.
            if ($this->commandExists('database:install')) {
                $this->artisan('database:install', ['--force' => true]);
            } else {
                $this->artisan('migrate:fresh');
            }

            $this->app[Kernel::class]->setArtisan(null);

            RefreshDatabaseState::$migrated = true;
        }
        // We don't use transactions.
        // But we need to close the DB connections in the tearDown method.
    }

    /**
     * Check if an Artisan command exists.
     *
     * @param  string  $name
     * @return bool
     */
    protected function commandExists(string $name): bool
    {
        return array_key_exists($name, Artisan::all());
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     *
     * @throws \Mockery\Exception\InvalidCountException
     */
    protected function tearDown(): void
    {
        $database = $this->app->make('db');

        foreach (Config::databaseConnections() as $name) {
            $driver = config('database.connections.' . $name . '.driver');

            // Disconnect only MySQL and PostgreSQL databases.
            if (in_array($driver, ['mysql', 'pgsql'])) {
                $connection = $database->connection($name);
                $connection->disconnect();
            }
        }
    }
}
