<?php
require __DIR__ . '/../vendor/autoload.php';

use FewAgency\Carbonator\Carbonator;
use Carbon\Carbon;

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

// Populate a HTML5 datetime-local input for a user in Japan:
$in_utc = Carbon::parse('2016-08-07 13:37');
echo Carbonator::parseToDatetimeLocal($in_utc, 'Asia/Tokyo');
// 2016-08-07T22:37:00

echo "\n\n";

// Populate a (deprecated) HTML5 datetime input
$in_namibia = Carbon::parse('2016-08-07 13:37 Africa/Windhoek');
// Windhoek is 1 hour ahead of UTC when not on daylight savings time
echo Carbonator::parseToDatetime($in_namibia);
// 2016-08-07T13:37:00+01:00

echo "\n\n";