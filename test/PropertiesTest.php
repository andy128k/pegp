<?php

use Pegp\Pegp as P;

class PropertiesTest extends ParserTestCase
{
    public function testProperties()
    {
        $p =
            P::oneOf(
                P::stri('default'),
                P::stri('us'),
                P::stri('european'),
                P::seq(
                    P::optional(
                        P::seq(
                            P::oneOf(
                                P::stri('big-endian'),
                                P::stri('little-endian'),
                                P::stri('middle-endian')),
                            P::re('\s+')->drop())->join()),
                    P::oneOf(
                        P::stri('slashes')->value('/'),
                        P::stri('dots')->value('.'),
                        P::stri('hyphens')->value('-'),
                        P::stri('spaces')->value(' '))));

        $this->assertParse('us', $p, 'us');
        $this->assertParse(array('big-endian', '-'), $p, 'Big-endian hyphEns');
        $this->assertParse(array('little-endian', '.'), $p, 'little-endian dots');
        $this->assertParse(array('middle-endian', '/'), $p, 'Middle-Endian Slashes');
        $this->assertParse(array('big-endian', ' '), $p, 'big-endian spaces');
        $this->assertParse(array('', '-'), $p, 'Hyphens');
    }

    public function testRegexp()
    {
        $p =
            P::seq(
                P::re('\s+')->drop(),
                P::re('\\d+')->group(0),
                P::re('\s+')->drop())->join();

        $this->assertParse('234', $p, '  234 ');
        $this->assertParseFailure($p, '');
    }
}
