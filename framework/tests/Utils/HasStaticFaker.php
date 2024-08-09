<?php

namespace Trax\Framework\Tests\Utils;

use Faker\Factory;

trait HasStaticFaker
{
    protected static $faker = null;

    protected static function initFaker()
    {
        if (is_null(self::$faker)) {
            self::$faker = Factory::create();
        }
    }

    public static function faker()
    {
        self::initFaker();
        return self::$faker;
    }
}
