# Carbonator
Datetime helpers built on [Carbon](http://carbon.nesbot.com).

## Installation & Configuration
> composer require fewagency/carbonator

## Usage

### The first parameter - `$input`
All of `Carbonator`'s methods take a `string` or
[PHP `DateTime`](http://php.net/manual/en/class.datetime.php)
instance as the *first* `$input` parameter.
This `$input` will be turned into a `Carbon` instance to process.
Remember that `Carbon` extends `DateTime`
and as such can always be used safely as input.
Any empty `$input` or any errors in parsing will make methods return `null`.

### The last parameter - `$tz_parse`
All of `Carbonator`'s methods also take the `$tz_parse` *timezone* as the *last* parameter.
The `$tz_parse` timezone will be used as context when parsing an `$input` string,
but will be ignored when parsing `DateTime` instances, as they already have a timezone.
As per default `DateTime` behaviour, it will also be ignored whenever there is timezone
information supplied *within the string*.

### Timezone parameters
If a timezone parameter is left out or `null` when calling a method,
the PHP default timezone from
[date_default_timezone_get()](http://php.net/manual/en/function.date-default-timezone-get.php)
will be used.

Timezones can be supplied in `string` format (e.g. `'Europe/Stockholm'` or `'+01:00'`) or a
[PHP `DateTimeZone`](http://php.net/manual/en/class.datetimezone.php) instance.
Hopefully your default timezone will be `UTC`.
Some frameworks, like Laravel, set this timezone explicitly using a config value
and you should keep that as `UTC` unless you really know what you're trying to achieve with
another setting. 

### Methods
`parse($input, $tz_parse = null)` returns a `Carbon` instance.
Note that the timezone of the output doesn't always match the supplied context timezone
from the 2nd parameter. The output is just guaranteed to be `Carbon` or `null`.

`parseToTz($input, $tz_target = null, $tz_parse = null)`
returns a `Carbon` instance, guaranteed to be in the `$tz_target` timezone.

`parseToDefaultTz($input, $tz_parse = null)` returns a `Carbon` instance,
guaranteed to be in the PHP default timezone.

`formatInTz($input, $format, $tz_target = null, $tz_parse = null)` returns a string
formatted according to the `$format` - see [docs for PHP's `date()`](http://php.net/manual/en/function.date.php).
The string is guaranteed to be in the `$tz_target` timezone.
If no `$tz_parse` timezone is supplied, the `$tz_target` parameter will be used as
parsing context too.

`parseToDatetimeLocal($input, $tz_target = null, $tz_parse = null)`
returns a string formatted for a
[HTML5 `datetime-local` input](http://www.w3.org/TR/html-markup/input.datetime-local.html)
in the `'Y-m-d\TH:i:s'` format, e.g. `2016-08-07T13:37:00`.

`parseToDatetime($input, $tz_parse = null)` returns a string formatted for a
[HTML5 `datetime` input](http://www.w3.org/TR/html-markup/input.datetime.html)
in the `DateTime::W3C` format *with a timezone*, e.g. `2016-08-07T13:37:00+01:00`.
The timezone in the resulting string will be the `$tz_parse` timezone only when no
timezone information was present in the `$input`, so make sure to move your
`DateTime` instance to the desired timezone *before* passing it as `$input`.

## Authors
I, Björn Nilsved, work at the largest communication agency in southern Sweden.
We call ourselves [FEW](http://fewagency.se) (oh, the irony).
From time to time we have positions open for web developers/programmers/UXers in the Malmö/Copenhagen area,
so please get in touch!

## License
FEW Agency's Carbonator is open-sourced software licensed under the
[MIT license](http://opensource.org/licenses/MIT)
