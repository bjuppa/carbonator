# Carbonator

[![Build Status](https://travis-ci.org/bjuppa/carbonator.svg?branch=master)](https://travis-ci.org/bjuppa/carbonator)

A collection of datetime helpers built on [Carbon](http://carbon.nesbot.com).
They help you easily parse or convert `string`s or `DateTime`s between timezones (and format them) with one single call.

A good pattern is to always keep your app working with times in the **UTC** timezone internally
and to store any time data in that default timezone.
If you're temped to or already use another timezone as default, please read the following article:
[Always Use UTC Dates And Times](https://medium.com/@kylekatarnls/always-use-utc-dates-and-times-8a8200ca3164).

This is how `Carbonator` can help you juggle those timezones:

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


// Format a time for a user in Namibia
$in_utc = Carbon::parse('2016-08-07 13:37');
// Windhoek is 1 hour ahead of UTC when not on daylight savings time (WAT: West Africa Time)
echo Carbonator::formatInTz($in_utc, 'D, M j, Y H:i T', 'Africa/Windhoek');
// Sun, Aug 7, 2016 14:37 WAT


// Populate a HTML5 datetime-local input for a user in Japan:
$in_utc = Carbon::parse('2016-08-07 13:37');
echo Carbonator::parseToDatetimeLocal($in_utc, 'Asia/Tokyo');
// 2016-08-07 22:37
```

## Installation & Configuration

> composer require fewagency/carbonator

## Background

The [Carbon](http://carbon.nesbot.com) package is excellent,
but it doesn't support parsing other `DateTime` instances while _keeping their timezone_.
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
instance as the _first_ `$input` parameter.

This `$input` will be turned into a `Carbon` instance to process.
Remember that `Carbon` extends `DateTime` and can always be used safely as input.

Any empty `$input` or any errors in parsing will make methods return `null`.

### The last parameter - `$tz_parse`

All of `Carbonator`'s methods also take the `$tz_parse` _timezone_ as the _last_ parameter.
The `$tz_parse` timezone will be used as context when parsing an `$input` string,
but will be ignored when parsing `DateTime` instances, as they already have a timezone.
As per default `DateTime` behaviour, `$tz_parse` will also be ignored whenever there is timezone
information supplied _within the string_.

### Timezone parameters

Timezones can be supplied in `string` format (e.g. `'Europe/Stockholm'` or `'+01:00'`) or as a
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

#### `Carbonator::parse($input, $tz_parse = null)`

Returns a `Carbon` instance.
Note that the timezone of the output doesn't always match the supplied context timezone
from the 2nd parameter. The output is just guaranteed to be `Carbon` or `null`.

#### `Carbonator::parseToTz($input, $tz_target = null, $tz_parse = null)`

Returns a `Carbon` instance, guaranteed to be in the `$tz_target` timezone.

#### `Carbonator::parseToDefaultTz($input, $tz_parse = null)`

Returns a `Carbon` instance, guaranteed to be in the PHP default timezone.

#### `Carbonator::formatInTz($input, $format, $tz_target = null, $tz_parse = null)`

Returns a string formatted according to the `$format` - see [docs for PHP's `date()`](http://php.net/manual/en/function.date.php).
The string is guaranteed to be in the `$tz_target` timezone.

#### `Carbonator::parseToDatetimeLocal($input, $tz_target = null, $tz_parse = null)`

Returns a string formatted for a
[HTML5 `datetime-local` input](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/datetime-local)
in the `'Y-m-d H:i'` format (e.g. `'2016-08-07 13:37'`), with no timezone information.
Be kind to your users and display the timezone next to your input, perhaps in the `<label>`
or in an element referenced through the input's `aria-describedby` attribute.

## Figuring out users' timezones

There's no way to know a user's timezone for sure, without asking them.
If you want to help your users select a timezone,
[PHP's `timezone_identifiers_list()`](http://php.net/manual/en/function.timezone-identifiers-list.php)
is a nice tool to find relevant timezones by country or continent.

The list is quite long though, so you could do IP-location on the server side to suggest a timezone to the user.
For Laravel there's a package called [Laravel Geoip](http://lyften.com/projects/laravel-geoip/) doing just that
by connecting to different services.

In the browser you could run some javascript, for example
[jstimezonedetect](https://www.npmjs.com/package/jstimezonedetect)
to guess the _named timezone_ of your user from the _timezone offset_ reported by their operating system.
Or you could use the browser's [Geolocation API](https://developer.mozilla.org/en-US/docs/Web/API/Geolocation_API),
but that could be seen as a bit intrusive by the user, with permissions and everything.

In Laravel, there's a handy [validation rule for timezone identifiers](https://laravel.com/docs/validation#rule-timezone).

## Check your setup

This console command will check your Linux system timezone:

> date +%Z

Hopefully it's `UTC`.

This query can be run by MySQL to check which timezones your database is using:

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
If the session timezone is wrong, an optional `'timezone'` parameter can be set on MySQL connections
in your app's `config/databases.php`.
Don’t set it manually unless you need to, in which case it should be the same as the
Laravel config, i.e `'UTC'`.
Actually, pulling in `config('app.timezone')` in the database config file works too!

## Other methods

Here's a good read on [Getting MySQL To Do It](https://www.mettle.io/blog/post/mysql-php-timezone/) when it comes to timezones and PHP.

## Authors

I, Björn Nilsved, created this package while working at [FEW](http://fewagency.se).
