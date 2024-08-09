<?php

namespace Trax\Framework\Xapi\Helpers;

use Illuminate\Support\Str;

class XapiDate
{
    /**
     * @var int
     */
    protected static $precision = 6;

    /**
     * Get the microtime: [seconds, microseconds].
     *
     * @return array
     */
    public static function microtime(): array
    {
        list($micro, $seconds) = explode(' ', microtime());
        $micro6 = substr(explode('0.', $micro)[1], 0, self::$precision);
        return [intval($seconds), $micro6];
    }

    /**
     * Get the present time.
     *
     * @return string
     */
    public static function now(): string
    {
        list($seconds, $micro) = self::microtime();
        return date('Y-m-d\TH:i:s.', $seconds) . $micro . 'Z';
    }

    /**
     * Normalize a given ISO date to enable later comparison between dates.
     *
     * @param  string  $isoDate
     * @return string
     */
    public static function normalize(string $isoDate): string
    {
        // Convert to Unix timestamp.
        // Microseconds are lost.
        // Timezone is taken into account.
        $timestamp = strtotime($isoDate);

        // We need to extract microseconds from the original ISO date and normalize it.

        // The delimiter may be 'T' or ' ', which are both accepted by the validation rules.
        $delimiter = Str::contains($isoDate, 'T') ? 'T' : ' ';
        $dateTime = explode($delimiter, $isoDate);

        // We may have only the date, not the time.
        $time = isset($dateTime[1]) ? $dateTime[1] : '00:00:00';

        // Remove the timezone at the end.
        if (strpos($time, '+') !== false) {
            list($time, $forget) = explode('+', $time);
        } elseif (strpos($time, '-') !== false) {
            list($time, $forget) = explode('-', $time);
        } elseif (strpos($time, 'Z') !== false) {
            list($time) = explode('Z', $time);
        }
        
        // Extract and normalize microseconds.
        $parts = explode('.', $time);
        $micro = count($parts) > 1
            ? substr($parts[1].'000000', 0, self::$precision)
            : '000000';

        return date('Y-m-d\TH:i:s.', $timestamp) . $micro . 'Z';
    }
}
