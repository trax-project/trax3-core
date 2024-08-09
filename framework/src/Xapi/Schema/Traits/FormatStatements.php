<?php

namespace Trax\Framework\Xapi\Schema\Traits;

use Trax\Framework\Xapi\Schema\Parsing\StatementSchema;
use Trax\Framework\Xapi\Schema\Parsing\Parser;

trait FormatStatements
{
    use ReorderStatement;

    /**
     * Format a statement.
     *
     * @param  object  $statement
     * @param  string  $format
     * @param  string  $lang
     * @return object
     */
    public static function format($statement, string $format = 'exact', $lang = null)
    {
        if ($format == 'ids') {
            $statement = self::formatIds($statement);
        } elseif ($format == 'canonical') {
            $statement =self::formatCanonical($statement, $lang);
        }
        return $statement;
    }
    
    /**
     * Return statement with the 'ids' format.
     *
     * @param  object  $statement
     * @return object
     */
    protected static function formatIds($statement)
    {
        $schema = new StatementSchema();
        $parser = new Parser($schema);
        return $parser->transform($statement, 'statement', function ($object, $prop, $schema) {

            // Remove all descriptive props.
            if (in_array('descriptive', $schema)) {
                unset($object->$prop);
            }
            
            // Remove objectType: 'Activity' as it is optional.
            if ($prop == 'objectType' && $object->$prop == 'Activity') {
                unset($object->$prop);
            }
        });
    }
    
    /**
     * Return statement with the 'canonical' format.
     *
     * @param  object  $statement
     * @param  string  $lang
     * @return object
     */
    protected static function formatCanonical($statement, $lang = null)
    {
        $schema = new StatementSchema();
        $parser = new Parser($schema);
        return $parser->transform($statement, 'statement', function ($object, $prop, $schema) use ($lang) {
            
            // Canonize lang maps
            if (isset($schema['format']) && $schema['format'] == 'xapi_lang_map') {
                $object->$prop = self::canonize($object->$prop, $lang);
            }
        });
    }

    /**
     * Canonize a lang string.
     *
     * @param  object  $langMap
     * @param  string  $lang
     * @return object
     */
    protected static function canonize($langMap, $headerLang = null)
    {
        $preferedLang = isset($headerLang) ? $headerLang : 'en';
        $langs = get_object_vars($langMap);
        
        // Search from exact prefered lang.
        foreach ($langs as $lang => $label) {
            if (substr($lang, 0, strlen($preferedLang)) == $preferedLang) {
                return (object)array($lang => $label);
            }
        }

        // Search from global prefered lang.
        $parts = explode('-', $preferedLang);
        if (count($parts) == 2) {
            $preferedLang = $parts[0];
            foreach ($langs as $lang => $label) {
                if (substr($lang, 0, strlen($preferedLang)) == $preferedLang) {
                    return (object)array($lang => $label);
                }
            }
        }

        // First lang.
        foreach ($langs as $lang => $label) {
            return (object)array($lang => $label);
        }
        
        // No lang
        return $langMap;
    }
}
