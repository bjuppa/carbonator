<?php

use FewAgency\Carbonator\Carbonator;
use Carbon\Carbon;

class CarbonatorTest extends PHPUnit_Framework_TestCase
{
    public function testParseToTzReturnsNullOnFail()
    {
        $this->assertNull(Carbonator::parseToTz('fail'));
    }

    public function testParseToTzReturnsNullOnEmptyString()
    {
        $this->assertNull(Carbonator::parseToTz(''));
    }

    public function testParseToTz()
    {
        $c = Carbonator::parseToTz('tomorrow 13:37');

        $this->assertEquals('13:37:00', $c->toTimeString());
        $this->assertTrue($c->utc);
    }

    public function testParseToTzWithTargetTz()
    {
        $c = Carbonator::parseToTz('tomorrow 13:37', 'Europe/Stockholm');

        $this->assertEquals('Europe/Stockholm', $c->tzName);
        $this->assertEquals('13:37:00', $c->toTimeString());
    }

    public function testParseToTzWithParseTz()
    {
        $c = Carbonator::parseToTz('tomorrow 13:37', '-1', '+1');

        $this->assertEquals('-01:00', $c->tzName);
        $this->assertEquals('11:37:00', $c->toTimeString());
    }

    public function testParseToDefaultTz()
    {
        $c = Carbonator::parseToDefaultTz('tomorrow 13:37');

        $this->assertTrue($c->utc);
        $this->assertEquals('13:37:00', $c->toTimeString());
    }

    public function testParseToDefaultTzWithParseTz()
    {
        $c = Carbonator::parseToDefaultTz('tomorrow 13:37', 'Europe/Stockholm');

        $this->assertTrue($c->utc);
        $this->assertNotEquals('13:37:00', $c->toTimeString());
    }

    public function testFormatInTz()
    {
        $this->assertEquals(
            '2016-08-05T12:37:00+00:00',
            Carbonator::formatInTz('2016-08-05 13:37 +01:00', Carbon::W3C)
        );
    }

    public function testFormatInTzWithTargetTz()
    {
        $this->assertEquals(
            '2016-08-05T14:37:00+02:00',
            Carbonator::formatInTz('2016-08-05 13:37 +01:00', Carbon::W3C, '+2')
        );
    }

    public function testParseToDatetimeLocal()
    {
        $this->assertEquals(
            '2016-08-05T12:37:00',
            Carbonator::parseToDatetimeLocal('2016-08-05 13:37 +01:00')
        );
    }

    public function testParseToDatetimeLocalWithTargetTz()
    {
        $this->assertEquals(
            '2016-08-05T13:37:00',
            Carbonator::parseToDatetimeLocal('2016-08-05 13:37 +01:00', '+1')
        );
    }

    public function testParseToDatetimeLocalWithParseTz()
    {
        $this->assertEquals(
            '2016-08-05T15:37:00',
            Carbonator::parseToDatetimeLocal('2016-08-05 13:37', '+1', '-1')
        );
    }
}
