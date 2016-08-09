# Carbonator
A collection of datetime helpers built on [Carbon](http://carbon.nesbot.com).
They help you easily parse or convert `string`s or `DateTime`s between timezones (and format them) with one single call.

A good pattern is to always keep your app working with times in the **UTC** timezone internally
and to store any time data in that default timezone.

1. Actively convert any times to UTC when user data enters the application.
This is the perfect job for `Carbonator::parseToDefaultTz($input, $tz_parse = null)`.
2. If needed, convert to the user's timezone just before display or when populating inputs.
`parseToDatetimeLocal($input, $tz_target = null, $tz_parse = null)`
is one of the methods that can be used to directly format a string for display.

## Examples
```php
// Create DateTime in any timezone and get it in your app's default timezone:
$in_sweden = Carbon::parse('2016-08-07 13:37', 'Europe/Stockholm');
// Stockholm is 2 hours ahead of UTC during daylight savings time
$in_utc = Carbonator::parseToDefaultTz($in_sweden);
echo $in_utc->toCookieString();
// Sunday, 07-Aug-2016 11:37:00 UTC


// Parse directly from a string in a timezone and get it in your app's default timezone:
$in_utc = Carbonator::parseToDefaultTz('2016-08-07 13:37', '-05:00');
echo $in_utc->toCookieString();
// Sunday, 07-Aug-2016 18:37:00 UTC


// Populate a datetime-local input for a user in Japan:
$in_utc = Carbon::parse('2016-08-07 13:37');
echo Carbonator::parseToDatetimeLocal($in_utc, 'Asia/Tokyo');
// 2016-08-07T22:37:00


// Populate a datetime input
$in_namibia = Carbon::parse('2016-08-07 13:37 Africa/Windhoek');
// Windhoek is 1 hour ahead of UTC when not on daylight savings time
echo Carbonator::parseToDatetime($in_namibia);
// 2016-08-07T13:37:00+01:00
```

## Installation & Configuration
> composer require fewagency/carbonator

## Background
The [Carbon](http://carbon.nesbot.com) package is excellent,
but it doesn't support parsing other `DateTime` instances while *keeping their timezone*.
That's just the behaviour of the [PHP `DateTime` constructor](http://php.net/manual/en/datetime.construct.php)
and not much to do about.

Also, `Carbon` throws exceptions on errors.
To be able to parse any user input without having to filter or validate it first,
I wanted wrappers that just returns `null` when input can't be parsed. 

I think it makes sense putting these opinionated parsing and display methods into a separate
helper package instead of having `Carbon` do even more work.

## Usage

### The first parameter - `$input`
All of `Carbonator`'s methods take a `string` or
[PHP `DateTime`](http://php.net/manual/en/class.datetime.php)
instance as the *first* `$input` parameter.

This `$input` will be turned into a `Carbon` instance to process.
Remember that `Carbon` extends `DateTime` and can always be used safely as input.

Any empty `$input` or any errors in parsing will make methods return `null`.

### The last parameter - `$tz_parse`
All of `Carbonator`'s methods also take the `$tz_parse` *timezone* as the *last* parameter.
The `$tz_parse` timezone will be used as context when parsing an `$input` string,
but will be ignored when parsing `DateTime` instances, as they already have a timezone.
As per default `DateTime` behaviour, `$tz_parse` will also be ignored whenever there is timezone
information supplied *within the string*.

### Timezone parameters
Timezones can be supplied in `string` format (e.g. `'Europe/Stockholm'` or `'+01:00'`) or a
[PHP `DateTimeZone`](http://php.net/manual/en/class.datetimezone.php) instance.

Remember, named timezones like `Europe/Stockholm` takes daylight savings time (summer time)
into account, whereas offset timezones like `+01:00` does not.
Use named timezones if you can, your users will appreciate it.

If a timezone parameter is left out or `null` when calling a method,
the PHP default timezone from
[date_default_timezone_get()](http://php.net/manual/en/function.date-default-timezone-get.php)
will be used.

Hopefully your default timezone will be `UTC`.
Some frameworks, like Laravel, set the timezone explicitly using a config value
and you should keep that as `UTC` unless you really know what you're trying to achieve with
another setting.
Read more below in [Check your setup](#checkyoursetup).

### Methods
#### `parse($input, $tz_parse = null)`
Returns a `Carbon` instance.
Note that the timezone of the output doesn't always match the supplied context timezone
from the 2nd parameter. The output is just guaranteed to be `Carbon` or `null`.

#### `parseToTz($input, $tz_target = null, $tz_parse = null)`
Returns a `Carbon` instance, guaranteed to be in the `$tz_target` timezone.

#### `parseToDefaultTz($input, $tz_parse = null)`
Returns a `Carbon` instance, guaranteed to be in the PHP default timezone.

#### `formatInTz($input, $format, $tz_target = null, $tz_parse = null)`
Returns a string formatted according to the `$format` - see [docs for PHP's `date()`](http://php.net/manual/en/function.date.php).
The string is guaranteed to be in the `$tz_target` timezone.

#### `parseToDatetimeLocal($input, $tz_target = null, $tz_parse = null)`
Returns a string formatted for a
[HTML5 `datetime-local` input](http://www.w3.org/TR/html-markup/input.datetime-local.html)
in the `'Y-m-d\TH:i:s'` format (e.g. `'2016-08-07T13:37:00'`).

#### `parseToDatetime($input, $tz_parse = null)`
Returns a string formatted for a [HTML5 `datetime` input](http://www.w3.org/TR/html-markup/input.datetime.html)
in the `DateTime::W3C` format *with a timezone* (e.g. `'2016-08-07T13:37:00+01:00'`).
The timezone in the resulting string will be the `$tz_parse` timezone only when no
timezone information was present in the `$input`, so make sure to move your
`DateTime` instance to the desired timezone *before* passing it as `$input`.

## Check your setup
This console command will check your Linux system timezone:
> date +%Z

Hopefully it's `UTC`.

This query can be run by MySql to check which timezones your database is using:
```mysql
SELECT @@global.time_zone, @@session.time_zone
```
Hopefully both display as `SYSTEM` or `UTC`.

From within PHP you can use
[date_default_timezone_get()](http://php.net/manual/en/function.date-default-timezone-get.php)
to check what default timezone is used.
If it's not `UTC`, find out where it's set by either:
- [`date.timezone` `php.ini` option](http://php.net/manual/en/datetime.configuration.php#ini.date.timezone)
- [date_default_timezone_set('UTC')](http://php.net/manual/en/function.date-default-timezone-set.php)

### In Laravel
If you're using Laravel, you set your app's default timezone in your app's `config/app.php`.

You can easily check a database-connection's timezone using the `artisan tinker` command from the terminal:
> php artisan tinker

Then run:

```php
DB::select('SELECT @@global.time_zone, @@session.time_zone');
```

Again, hopefully both are reported as `SYSTEM` or `UTC`.
If the session timezone is wrong, an optional `'timezone'` parameter can be set on mysql connections
in your app's `config/databases.php`.
Don’t set it manually unless you need to, in which case it should be the same as the
Laravel config, i.e `'UTC'`.
Actually, pulling in `config('app.timezone')` in the database config file works too!.

## Authors
I, Björn Nilsved, work at the largest communication agency in southern Sweden.
We call ourselves [FEW](http://fewagency.se) (oh, the irony).
From time to time we have positions open for web developers/programmers/UXers
in the Malmö/Copenhagen area, so please get in touch!

## License
FEW Agency's Carbonator is open-sourced software licensed under the
[MIT license](http://opensource.org/licenses/MIT)
