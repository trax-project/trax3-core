<?php

namespace Trax\Framework\Database;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Trax\Framework\Context;
use Trax\Framework\Repo\Query;
use Trax\Framework\Xapi\Helpers\XapiDate;
use Trax\Framework\Xapi\Helpers\XapiAgent;
use Trax\Framework\Xapi\Helpers\Json;

abstract class Database
{
    /**
     * @var string
     */
    protected $service;                  // MUST BE DEFINED
    
    /**
     * @var string
     */
    protected static $connection;        // SHOULD BE DEFINED
    
    /**
     * @var array
     */
    protected static $tables = [         // SHOULD BE DEFINED ('type' is currently unused)
    //     'xapi_statements' => ['type' => 'main'],
    //     'xapi_verbs' => ['type' => 'index'],
    ];

    /**
     * @var array
     */
    protected $storeTables = [];         // SHOULD BE DEFINED

    /**
     * @var bool
     */
    protected $nosqlSupported = false;   // SHOULD BE DEFINED

    /**
     * @var \Trax\Framework\Repo\RepositoryInterface
     */
    protected $mainRepository;          // SHOULD BE DEFINED

    /**
     * @var \Trax\Framework\Database\DataRecorder
     */
    protected $recorder;                // SHOULD BE DEFINED

    /**
     * Is NoSQL supported?
     *
     * @return bool
     */
    public function nosqlSupported()
    {
        return $this->nosqlSupported;
    }

    /**
     * Is a database storable?
     * In other words, does it contain tables which have a store column?
     *
     * @return bool
     */
    public function isStorable()
    {
        return !empty($this->storeTables);
    }

    /**
     * Get a repository.
     *
     * @param  string  $table
     * @return \Trax\Framework\Repo\RepositoryInterface|null
     */
    public function repository(string $table)
    {
        if (in_array($table, self::tables()) && isset($this->$table)) {
            return $this->$table;
        }
        return null;
    }

    /**
     * Get the connection.
     *
     * @return string
     */
    public static function connection(): string
    {
        return static::$connection == 'default'
            ? config('database.default')
            : static::$connection;
    }

    /**
     * Get the tables.
     *
     * @return array
     */
    public static function tables(): array
    {
        return array_keys(static::$tables);
    }

    /**
     * Get the table definitions.
     *
     * @return array
     */
    public static function tableDefinitions(): array
    {
        return static::$tables;
    }

    /**
     * Clear data.
     *
     * @param  string  $until
     * @return void
     */
    public function clear(string $until = null): void
    {
        foreach (self::tableDefinitions() as $name => $definition) {
            $this->clearTable($name, $until);
        }
    }

    /**
     * Clear data from a table.
     *
     * @param  string  $table
     * @param  string  $until
     * @return void
     */
    public function clearTable(string $table, string $until = null): void
    {
        $filters = [];

        if (isset($until)) {
            $filters['stored'] = ['$lte' => XapiDate::normalize($until)];
        }

        $this->$table->deleteByQuery(new Query([
            'filters' => $filters
        ]));
    }

    /**
     * Clear users data.
     *
     * @param  array  $users
     * @return void
     */
    public function clearUsers(array $users): void
    {
        // Should be implemented by all the concerned databases.
    }

    /**
     * Clear store data.
     *
     * @param  string  $slug
     * @return void
     */
    public function clearStore(string $slug): void
    {
        foreach ($this->storeTables as $table) {
            $this->$table->deleteByQuery(
                new Query(['filters' => [
                    'store' => $slug,
                ]])
            );
        }
    }

    /**
     * Get a global status.
     *
     * @return object
     */
    public function globalStatus(): object
    {
        // When tables are provided, use the repo checkStatus function of the first table.
        foreach (self::tables() as $table) {
            return $this->$table->checkStatus();
        }

        // There is no table defined. Default manager. Default DB.
        // Currently no check.
        return (object)['ready' => true, 'reason' => ''];
    }

    /**
     * Show a status in the console.
     *
     * @param  \Illuminate\Console\Command  $console
     * @return void
     */
    public function consoleStatus(Command $console): void
    {
        $console->info('Database:');
        $console->line('Driver: ' . config('database.connections.' . self::connection() . '.driver'));
        $console->line('Host: ' . config('database.connections.' . self::connection() . '.host'));
        $console->line('Port: ' . config('database.connections.' . self::connection() . '.port'));
        $console->line('');

        foreach (self::tables() as $table) {
            $console->info($table . ' table:');
            $status = $this->$table->checkStatus();
            if (!$status->ready) {
                $console->error($status->reason);
                $console->line('');
            } else {
                $console->line('Status: ready');
                $console->line('');
            }
        }
    }

    /**
     * Export data from a repository.
     *
     * @param  mixed  $from
     * @param  mixed  $to
     * @param  int  $count
     * @param  bool  $rawFormat
     * @param  string  $sortableProp
     * @return \Illuminate\Support\Collection
     *
     * @throws \Exception
     */
    public function export($from = null, $to = null, $count = 100, bool $rawFormat = false, string $sortableProp = 'stored'): Collection
    {
        if (empty($this->mainRepository)) {
            throw new \Exception("The main repository is not defined. We can't export.");
        }

        $query = [
            'filters' => $this->exportFilters(),
            'limit' => $count,
            'sort' => [$sortableProp],
        ];

        // From.
        if (isset($from)) {
            $query['filters'][] = [$sortableProp => ['$gt' => $from]];
        }

        // To.
        if (isset($to)) {
            $query['filters'][] = [$sortableProp => ['$lte' => $to]];
        }

        // Get the items.
        $models = $this->mainRepository->get(new Query($query));

        // Apply pseudonymization.
        $pseudos = [];
        if (Context::pipeline()->pseudonymize_statements) {
            $models = $models->map(function ($model) use (&$pseudos) {
                // We assume pseudonymization only applies to statements.
                $model->pseudonymized = true;
                $model->raw = XapiAgent::pseudonymizeStatement($model->raw, $pseudos);
                return $model;
            });
        }
        
        // Export models.
        return $models->map(function ($model) use ($rawFormat) {
            return $this->mainRepository->factory()->export($model, $rawFormat);
        });
    }

    /**
     * Export filters.
     *
     * @return array
     */
    protected function exportFilters(): array
    {
        return [];
    }

    /**
     * Import data in a repository.
     *
     * @param  \Illuminate\Support\Collection  $batch
     * @return void
     *
     * @throws \Exception
     */
    public function import(Collection $batch): void
    {
        if (empty($this->mainRepository)) {
            throw new \Exception("The main repository is not defined. We can't import.");
        }
        
        if (empty($this->recorder)) {
            throw new \Exception("The recorder is not defined. We can't import.");
        }
        
        // Prepare data.
        $records = $batch->map(function ($item) {

            // Be sure to work with objects.
            return $this->mainRepository->factory()->import(
                Json::object($item)
            );
        })->all();

        // Record.
        $this->recorder->record($records);
    }
}
