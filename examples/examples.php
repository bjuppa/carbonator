<?php
require __DIR__ . '/../vendor/autoload.php';

use Carbon\Carbon;
use FewAgency\Carbonator\Carbonator;

date_default_timezone_set('UTC');

echo "\n";

// Create DateTime in any timezone and get it in your app's default timezone:
$in_sweden = Carbon::parse('2016-08-07 13:37', 'Europe/Stockholm');
// Stockholm is 2 hours ahead of UTC during daylight savings time
$in_utc = Carbonator::parseToDefaultTz($in_sweden);
echo $in_utc->toCookieString();
// Sunday, 07-Aug-2016 11:37:00 UTC

echo "\n\n";

// Parse directly from a string in a timezone and get it in your app's default timezone:
$in_utc = Carbonator::parseToDefaultTz('2016-08-07 13:37', '-05:00');
echo $in_utc->toCookieString();
// Sunday, 07-Aug-2016 18:37:00 UTC

echo "\n\n";

// Format a time for a user in Namibia
$in_utc = Carbon::parse('2016-08-07 13:37');
// Windhoek is 1 hour ahead of UTC when not on daylight savings time (WAT: West Africa Time)
echo Carbonator::formatInTz($in_utc, 'D, M j, Y H:i T', 'Africa/Windhoek');
// Sun, Aug 7, 2016 14:37 WAT

echo "\n\n";

// Populate a HTML5 datetime-local input for a user in Japan:
$in_utc = Carbon::parse('2016-08-07 13:37');
echo Carbonator::parseToDatetimeLocal($in_utc, 'Asia/Tokyo');
// 2016-08-07T22:37

echo "\n\n";
