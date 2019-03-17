<?php

use Pegp\Input;
use Pegp\Parser\Parser;
use Pegp\Result;
use PHPUnit\Framework\TestCase;

abstract class ParserTestCase extends TestCase
{
    public function assertParse($expected, Parser $parser, $input)
    {
        $input = new Input($input);
        $result = $parser->parse($input);

        $this->assertParseResultSucceed($result);
        $this->assertInputExhausted($input, $result->input);

        $this->assertEquals($expected, $result->result);
        return $result->result;
    }

    public function assertParseFailure(Parser $parser, $input)
    {
        $input = new Input($input);
        $result = $parser->parse($input);

        $this->assertParseResultFailed($result);
    }

    /**
     * @param Result $result
     */
    public function assertParseResultSucceed(Result $result)
    {
        $this->assertInstanceOf(Result::class, $result);
        $this->assertInstanceOf(Input::class, $result->input);
        if (!$result->isSuccess()) {
            $this->fail(sprintf("Parse failed. Expected %s at %s.", $result->result->getComment(), $result->input->pos));
        }
    }

    /**
     * @param Result $result
     */
    private function assertParseResultFailed(Result $result)
    {
        $this->assertInstanceOf(Result::class, $result);
        $this->assertFalse($result->isSuccess());
        $this->assertInstanceOf(Input::class, $result->input);
    }

    /**
     * @param Input $inputBefore
     * @param Input $inputAfter
     */
    public function assertInputExhausted(Input $inputBefore, Input $inputAfter)
    {
        $this->assertInstanceOf(Input::class, $inputBefore);
        $this->assertInstanceOf(Input::class, $inputAfter);

        $this->assertEquals($inputBefore->data, $inputAfter->data);
        $this->assertEquals($inputBefore->length, $inputAfter->length);
        $this->assertEquals($inputBefore->length, $inputAfter->pos);
    }
}
