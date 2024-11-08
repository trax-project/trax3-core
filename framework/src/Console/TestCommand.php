<?php

namespace Trax\Framework\Console;

use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand as LaravelTestCommand;

class TestCommand extends LaravelTestCommand
{
    /**
     * Get the configuration file.
     *
     * @return string
     */
    protected function getConfigurationFile()
    {
        // Use the phpunit.xml.local when the trax3 package is installed locally.
        if (file_exists(base_path('trax/core/composer.json'))) {
            return base_path('phpunit.local.xml');
        }

        return parent::getConfigurationFile();
    }
}
