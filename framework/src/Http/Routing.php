<?php

namespace Trax\Framework\Http;

use Trax\Framework\Service\Config;

class Routing
{
    const API_AND_WEB = 0;
    const API_ONLY = 1;
    const WEB_ONLY = 2;

    /**
     * @return int
     */
    public static function dataApiMode(): int
    {
        return Config::dataApi() ? self::API_AND_WEB : self::WEB_ONLY;
    }

    /**
     * @return int
     */
    public static function jobsApiMode(): int
    {
        return Config::jobsApi() ? self::API_AND_WEB : self::WEB_ONLY;
    }

    /**
     * @return int
     */
    public static function loggingApiMode(): int
    {
        return Config::loggingApi() ? self::API_AND_WEB : self::WEB_ONLY;
    }

    /**
     * @return int
     */
    public static function accessApiMode(): int
    {
        return Config::accessApi() ? self::API_AND_WEB : self::WEB_ONLY;
    }
}
