<?php

class PropertiesTest extends PHPUnit_Framework_TestCase
{
    public function assertParse($expected, $parser, $input)
    {
        $i = new PegpInput($input);
        $r = $parser->parse($i);
        $this->assertInstanceOf('PegpResult', $r);
        $inputAfter = $r->input;
        $this->assertInstanceOf('PegpInput', $inputAfter);

        if (!$r->isSuccess()) {
            echo 'Parse failed. Expected ' . $r->result->getComment() . ' at ' . $r->input->pos . "\n";
        }

        $this->assertTrue($r->isSuccess());
        $this->assertEquals($i->data, $inputAfter->data);
        $this->assertEquals($i->length, $inputAfter->length);
        $this->assertEquals($i->length, $inputAfter->pos);
        $this->assertEquals($expected, $r->result);
    }

    public function testProperties()
    {
        $p =
        Pegp::oneOf(
            Pegp::stri('default'),
            Pegp::stri('us'),
            Pegp::stri('european'),
            Pegp::seq(
                Pegp::optional(
                    Pegp::seq(
                        Pegp::oneOf(
                            Pegp::stri('big-endian'),
                            Pegp::stri('little-endian'),
                            Pegp::stri('middle-endian')),
                        Pegp::re('\s+')->drop())->join()),
                Pegp::oneOf(
                    Pegp::stri('slashes')->value('/'),
                    Pegp::stri('dots')->value('.'),
                    Pegp::stri('hyphens')->value('-'),
                    Pegp::stri('spaces')->value(' '))));

        $this->assertParse('us', $p, 'us');
        $this->assertParse(array('big-endian', '-'), $p, 'Big-endian hyphEns');
        $this->assertParse(array('little-endian', '.'), $p, 'little-endian dots');
        $this->assertParse(array('middle-endian', '/'), $p, 'Middle-Endian Slashes');
        $this->assertParse(array('big-endian', ' '), $p, 'big-endian spaces');
        $this->assertParse(array('', '-'), $p, 'Hyphens');
    }
}

