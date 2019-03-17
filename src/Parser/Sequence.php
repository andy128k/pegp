<?php

namespace Pegp\Parser;

use Pegp\Input;
use Pegp\Result;

class Sequence extends Parser
{
    /** @var Parser[] */
    private $parsers;

    /** @var string */
    private $op = null;

    /**
     * @param Parser[] $parsers
     */
    public function __construct(array $parsers)
    {
        $this->parsers = $parsers;
    }

    /**
     * @param Input $input
     * @return Result
     */
    public function doParse(Input $input)
    {
        $inp = $input;
        $result = array();
        foreach ($this->parsers as $parser) {
            $r = $parser->parse($inp);
            if ($r->isSuccess()) {
                $inp = $r->input;
                if ($r->result !== null)
                    $result[] = $r->result;
            } else {
                return Result::failure($inp, $parser);
            }
        }
        return Result::success($inp, $result);
    }

    /**
     * @param Result $result
     * @return Result
     */
    protected function process($result)
    {
        switch ($this->op) {
            case 'join':
                $result = Result::success($result->input, implode('', $result->result));
                break;
            case 'bitOr':
                $value = 0;
                foreach ($result->result as $v)
                    $value |= $v;
                $result = Result::success($result->input, $value);
                break;
            case 'sum':
                $value = 0;
                foreach ($result->result as $v)
                    $value += $v;
                $result = Result::success($result->input, $value);
                break;
            case 'product':
                $value = 1;
                foreach ($result->result as $v)
                    $value *= $v;
                $result = Result::success($result->input, $value);
                break;
        }
        return parent::process($result);
    }

    /**
     * @return Sequence
     */
    public function join()
    {
        $this->op = 'join';
        return $this;
    }

    /**
     * @return Sequence
     */
    public function bitOr()
    {
        $this->op = 'bitOr';
        return $this;
    }

    /**
     * @return Sequence
     */
    public function sum()
    {
        $this->op = 'sum';
        return $this;
    }

    /**
     * @return Sequence
     */
    public function product()
    {
        $this->op = 'product';
        return $this;
    }
}
