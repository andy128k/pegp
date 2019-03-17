<?php

namespace Pegp\Parser;

use Pegp\Input;
use Pegp\Result;

class Optional extends Parser
{
    /** @var Parser */
    private $parser;

    /** @var string */
    private $emptyValue;

    /**
     * @param Parser $parser
     * @param string $emptyValue
     */
    public function __construct(Parser $parser, $emptyValue='')
    {
        $this->parser = $parser;
        $this->emptyValue = $emptyValue;
    }

    /**
     * @param Input $input
     * @return Result
     */
    public function doParse(Input $input)
    {
        $r = $this->parser->parse($input);
        if ($r->isSuccess()) {
            return $r;
        } else {
            return Result::success($input, $this->emptyValue);
        }
    }
}
