<?php
namespace FewAgency\Carbonator;

use Carbon\Carbon;
use DateTime;
use DateTimeZone;

class Carbonator
{
    /**
     * @param string|DateTime $input
     * @param string|DateTimeZone $tz_target
     * @param string|DateTimeZone $tz_parse
     * @return Carbon|null
     */
    public static function parseToTz($input, $tz_target = null, $tz_parse = null)
    {
        if ($input instanceof DateTime) {
            // Any instances of DateTime (e.g. Carbon) can be used with their current timezone
            // We ignore $tz_parse, just as parsing a string including timezone does
            $input = Carbon::instance($input);
        } elseif (strlen($input)) {
            // Not parsing if "empty", but lets 0 through - it will be interpreted as unix timestamp
            try {
                // Strings are parsed in the specified timezone or the target timezone
                $input = Carbon::parse($input, $tz_parse ?: $tz_target);
            } catch (\Exception $e) {
                // Any failed parsing
                return null;
            }
        } else {
            // Avoiding empty string interpreted as 'now' if parsed
            return null;
        }

        // Move the time into the target (or default) timezone
        return $input->tz($tz_target);
    }

    /**
     * @param string|DateTime $input
     * @param string|DateTimeZone $tz_parse
     * @return Carbon|null
     */
    public static function parseToDefaultTz($input, $tz_parse = null)
    {
        // Parse in the specified timezone, and then always move to default timezone
        return self::parseToTz($input, null, $tz_parse);
    }

    /**
     * @param string|DateTime $input
     * @param string $format
     * @param string|DateTimeZone $tz
     * @return string|null
     */
    public static function formatInTz($input, $format, $tz=null) {
        if($c = self::parseToTz($input, $tz)) {
            return $c->format($format);
        }
        return null;
    }
}