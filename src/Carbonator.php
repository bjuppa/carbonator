<?php
namespace FewAgency\Carbonator;

use Carbon\Carbon;
use DateTime;
use DateTimeZone;

class Carbonator
{
    const DATETIMELOCAL = 'Y-m-d\TH:i:s';

    /**
     * @param string|DateTime $input
     * @param string|DateTimeZone $tz_parse
     * @return Carbon|null
     */
    public static function parse($input, $tz_parse = null)
    {
        if ($input instanceof DateTime) {
            // Any instances of DateTime (e.g. Carbon) can be used with their current timezone
            // We ignore $tz_parse, just as parsing a string including timezone does
            return Carbon::instance($input);
        }

        if (!is_array($input) and strlen($input)) {
            // Not parsing if "empty", but lets 0 through - it will be interpreted as unix timestamp
            try {
                // Strings are parsed in the specified or default timezone
                return Carbon::parse($input, $tz_parse);
            } catch (\Exception $e) {
                // Any failed parsing
            }
        }

        return null;
    }

    /**
     * @param string|DateTime $input
     * @param string|DateTimeZone $tz_target
     * @param string|DateTimeZone $tz_parse
     * @return Carbon|null
     */
    public static function parseToTz($input, $tz_target = null, $tz_parse = null)
    {
        $input = self::parse($input, $tz_parse);

        if ($input instanceof Carbon) {
            // Move the time into the target (or default) timezone
            try {
                $input->tz($tz_target);
            } catch (\Exception $e) {
                // Invalid timezone
                return null;
            }
        }

        return $input;
    }

    /**
     * @param string|DateTime $input
     * @param string|DateTimeZone $tz_parse
     * @return Carbon|null
     */
    public static function parseToDefaultTz($input, $tz_parse = null)
    {
        // Parse in the specified timezone, and then move to default timezone
        return self::parseToTz($input, null, $tz_parse);
    }

    /**
     * @param string|DateTime $input
     * @param string $format
     * @param string|DateTimeZone $tz_target
     * @param string|DateTimeZone $tz_parse
     * @return string|null
     */
    public static function formatInTz($input, $format, $tz_target = null, $tz_parse = null)
    {
        if ($c = self::parseToTz($input, $tz_target, $tz_parse)) {
            return $c->format($format);
        }

        return null;
    }

    /**
     * @param string|DateTime $input
     * @param string|DateTimeZone $tz_target
     * @param string|DateTimeZone $tz_parse
     * @return string|null
     */
    public static function parseToDatetimeLocal($input, $tz_target = null, $tz_parse = null)
    {
        return self::formatInTz($input, self::DATETIMELOCAL, $tz_target, $tz_parse);
    }

    /**
     * @param string|DateTime $input
     * @param string|DateTimeZone $tz_parse
     * @return string|null
     */
    public static function parseToDatetime($input, $tz_parse = null)
    {
        if ($c = self::parse($input, $tz_parse)) {
            return $c->toW3cString();
        }

        return null;
    }
}
