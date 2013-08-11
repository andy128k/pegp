<?php

class CalculatorTest extends PHPUnit_Framework_TestCase
{
    public function assertParse($parser, $input)
    {
        $i = new PegpInput($input);
        $r = $parser->parse($i);
        $this->assertInstanceOf('PegpResult', $r);
        $inputAfter = $r->input;
        $this->assertInstanceOf('PegpInput', $inputAfter);

        if (!$r->isSuccess()) {
            echo 'Parse failed. Expected ' . $r->result . ' at ' . $r->input->pos . "\n";
        }

        $this->assertTrue($r->isSuccess());
        $this->assertEquals($i->data, $inputAfter->data);
        $this->assertEquals($i->length, $inputAfter->length);
        $this->assertEquals($i->length, $inputAfter->pos);
        return $r->result;
    }

    public function testString()
    {
        $p = Pegp::str('test');
        $this->assertParse($p, 'test');
    }

    public function testRegex()
    {
        $p = Pegp::re('te[xs]t');
        $this->assertParse($p, 'test');
        $this->assertParse($p, 'text');
    }

    public function testSQLStringLiteral()
    {
        $p = Pegp::re("'([^\\\\']*[\\\\']')*[^\\\\']*'");
        $this->assertParse($p, "''");
        $this->assertParse($p, "'Hello there'");
        $this->assertParse($p, "'Don\\'t worry. Be happy!'");
        $this->assertParse($p, "'Don''t worry. Be happy!'");
    }

    public function testDecimal()
    {
        $p = Pegp::re("\\d+(\\.\\d+)?");
        $r = $this->assertParse($p, '123.45');
        $this->assertEquals('.45', $r[1]);
    }

    public function testSequence()
    {
        $word = Pegp::re('[a-z]+');
        $space = Pegp::re('\s+');
        $optSpace = Pegp::re('\s*');
        $punctuation = Pegp::re('[.!?]')->setComment('punctiation mark');
        $p = Pegp::seq($optSpace, $word, $space, $word, $space, $word, $optSpace, $punctuation, $optSpace);

        $r = $this->assertParse($p, "\thow  are you  \n ? ");
    }
}

