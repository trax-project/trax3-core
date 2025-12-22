<?php

namespace Trax\Framework\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Trax\Framework\Service\Config;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Force UTF-8 output for Windows console
        if (PHP_OS_FAMILY === 'Windows' && function_exists('sapi_windows_cp_set')) {
            sapi_windows_cp_set(65001);
        }
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
        
        parent::tearDown();
    }
}
