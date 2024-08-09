<?php

namespace Trax\Framework\Service\Utils;

use Illuminate\Support\Facades\App;

class Env
{
    /**
     * Set or update env-variable.
     *
     * @param  array  $vars
     * @param  bool  $testing
     * @return bool
     */
    public static function set(array $vars, bool $testing = false): bool
    {
        $file = App::environmentFilePath();
        if (!file_exists($file)) {
            file_put_contents($file, '');
        }
        if ($testing) {
            if (!file_exists("$file.testing")) {
                file_put_contents("$file.testing", file_get_contents($file));
            }
            $file .= '.testing';
        }
        $content = file_get_contents($file);
        foreach ($vars as $key => $value) {
            [$content, $isNewVariableSet] = self::setEnvVariable($content, $key, $value);
        }
        return self::writeFile($file, $content . "\n");
    }

    /**
     * Convert a service key to env name.
     *
     * @param string $key
     * @return string
     */
    public static function key(string $key): string
    {
        return str_replace('-', '_', strtoupper($key));
    }


    // ------------------------------------------------------------------------------------------------
    // From https://github.com/imliam/laravel-env-set-command/blob/master/src/EnvironmentSetCommand.php
    // ------------------------------------------------------------------------------------------------
    
    
    /**
     * Set or update env-variable.
     *
     * @param string $envFileContent Content of the .env file.
     * @param string $key            Name of the variable.
     * @param string $value          Value of the variable.
     *
     * @return array [string newEnvFileContent, bool isNewVariableSet].
     */
    protected static function setEnvVariable(string $envFileContent, string $key, string $value): array
    {
        $oldPair = self::readKeyValuePair($envFileContent, $key);

        // Wrap values that have a space or equals in quotes to escape them
        if (preg_match('/\s/', $value) || strpos($value, '=') !== false) {
            $value = '"' . $value . '"';
        }

        $newPair = $key . '=' . $value;

        // For existed key.
        if ($oldPair !== null) {
            $replaced = preg_replace('/^' . preg_quote($oldPair, '/') . '$/uimU', $newPair, $envFileContent);
            return [$replaced, false];
        }

        // For a new key.
        return [$envFileContent . "\n" . $newPair, true];
    }

    /**
     * Read the "key=value" string of a given key from an environment file.
     * This function returns original "key=value" string and doesn't modify it.
     *
     * @param string $envFileContent
     * @param string $key
     *
     * @return string|null Key=value string or null if the key is not exists.
     */
    protected static function readKeyValuePair(string $envFileContent, string $key): ?string
    {
        // Match the given key at the beginning of a line
        if (preg_match("#^ *{$key} *= *[^\r\n]*$#uimU", $envFileContent, $matches)) {
            return $matches[0];
        }

        return null;
    }

    /**
     * Overwrite the contents of a file.
     *
     * @param string $path
     * @param string $content
     *
     * @return boolean
     */
    protected static function writeFile(string $path, string $content): bool
    {
        return (bool)file_put_contents($path, $content, LOCK_EX);
    }
}
