<?php
namespace FewAgency\Carbonator;

use Carbon\Carbon;
use DateTime;
use DateTimeZone;

class Carbonator
{
    const DATETIMELOCAL = 'Y-m-d\TH:i:s';
    const FLOATING_TZ_TARGET = true;

    /**
     * @param string|DateTime $input
     * @param string|DateTimeZone $tz_target
     * @param string|DateTimeZone $tz_parse
     * @param bool $floating_tz_target set to true to not move the result into the target timezone
     * @return Carbon|null
     */
    public static function parseToTz($input, $tz_target = null, $tz_parse = null, $floating_tz_target = false)
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

        if (!$floating_tz_target) {
            // Move the time into the target (or default) timezone
            $input->tz($tz_target);
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
        // Parse in the specified timezone, and then always move to default timezone
        return self::parseToTz($input, null, $tz_parse);
    }

    /**
     * @param string|DateTime $input
     * @param string $format
     * @param string|DateTimeZone $tz_target
     * @param string|DateTimeZone $tz_parse
     * @param bool $floating_tz_target set to true to not move the result into the target timezone
     * @return string|null
     */
    public static function formatInTz($input, $format, $tz_target = null, $tz_parse = null, $floating_tz_target = false)
    {
        if ($c = self::parseToTz($input, $tz_target, $tz_parse, $floating_tz_target)) {
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
        return self::formatInTz($input, Carbon::W3C, null, $tz_parse, self::FLOATING_TZ_TARGET);
    }
}