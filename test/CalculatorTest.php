<?php

use Pegp\Pegp;

class CalculatorTest extends ParserTestCase
{
    public function testString()
    {
        $p = Pegp::str('test');
        $this->assertParse("test", $p, 'test');
    }

    public function testRegex()
    {
        $p = Pegp::re('te[xs]t');
        $this->assertParse(["test"], $p, 'test');
        $this->assertParse(["text"], $p, 'text');
    }

    public function testSQLStringLiteral()
    {
        $p = Pegp::re("'([^\\\\']*[\\\\']')*[^\\\\']*'");
        $this->assertParse(["''"], $p, "''");
        $this->assertParse(["'Hello there'"], $p, "'Hello there'");
        $this->assertParse(["'Don\\'test worry. Be happy!'", "Don\\'"], $p, "'Don\\'test worry. Be happy!'");
        $this->assertParse(["'Don''test worry. Be happy!'", "Don''"], $p, "'Don''test worry. Be happy!'");
    }

    public function testDecimal()
    {
        $p = Pegp::re("\\d+(\\.\\d+)?");
        $r = $this->assertParse(["123.45", ".45"], $p, '123.45');
        $this->assertEquals('.45', $r[1]);
    }

    public function testSequence()
    {
        $word = Pegp::re('[a-z]+');
        $space = Pegp::re('\s+');
        $optSpace = Pegp::re('\s*');
        $punctuation = Pegp::re('[.!?]')->setComment('punctuation mark');
        $p = Pegp::seq($optSpace, $word, $space, $word, $space, $word, $optSpace, $punctuation, $optSpace);

        $this->assertParse([["\t"], ['how'], ['  '], ['are'], [' '], ['you'], ["  \n "], ['?'], [' ']], $p, "\thow  are you  \n ? ");
    }
}
