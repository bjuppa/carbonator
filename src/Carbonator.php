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
        // Parse in target timezone if parse timezone not specified
        $tz_parse = $tz_parse ?: $tz_target;

        if ($input instanceof DateTime) {
            $input = Carbon::instance($input);
        } elseif (strlen($input)) {
            try {
                $input = Carbon::parse($input, $tz_parse);
            } catch (\Exception $e) {
                return null;
            }
        } else {
            return null;
        }

        return $input->tz($tz_target);
    }
}