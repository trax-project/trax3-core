<?php

namespace Trax\Framework\Repo\Eloquent;

use InvalidArgumentException;

class GrammarFactory
{
    /**
     * Make a grammar class depending of the DB settings.
     *
     * @param  string  $model
     * @return \Trax\Framework\Repo\Eloquent\Grammar
     *
     * @throws \InvalidArgumentException
     */
    public static function make(string $model): Grammar
    {
        $driver = config('database.connections.' . (new $model)->getConnectionName() . '.driver');
        switch ($driver) {
            case 'mysql':
                return new MySqlGrammar();
            case 'pgsql':
                return new PostgreSqlGrammar();
        }
        throw new InvalidArgumentException("Unsupported database driver [{$driver}].");
    }
}
