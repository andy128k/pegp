<?php

namespace Pegp\Parser;

use Exception;
use Pegp\Result;
use Pegp\Input;

abstract class Parser
{
    /** @var string|null */
    public $comment = null;

    /** @var bool */
    private $drop = false;

    /** @var mixed */
    private $value = null;

    /**
     * @param string $string
     * @return mixed|null
     * @throws Exception
     */
    public function parseString($string)
    {
        $input = new Input($string);
        $result = $this->parse($input);
        if (!$result->isSuccess())
            throw new Exception('Parse failed. Expected ' . $result->result->getComment() . ' at ' . $result->input->pos);
        if ($input->length != $result->input->pos)
            throw new Exception('Parse failed. Unparsed tail at ' . $result->input->pos);
        return $result->result;
    }

    /**
     * @param Input $input
     * @return Result
     */
    public function parse(Input $input)
    {
        $result = $this->doParse($input);
        if ($result->isSuccess()) {
            return $this->process($result);
        } else {
            return $result;
        }
    }

    /**
     * @param Input $input
     * @return Result
     */
    public abstract function doParse(Input $input);

    /**
     * @param Result $result
     * @return Result
     */
    protected function process($result)
    {
        if ($this->drop) {
            return Result::success($result->input, null);
        } elseif ($this->value !== null) {
            return Result::success($result->input, $this->value);
        } else {
            return $result;
        }
    }

    /**
     * @return string|null
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return Parser
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return Parser
     */
    public function drop()
    {
        $this->drop = true;
        return $this;
    }

    /**
     * @param mixed $value
     * @return Parser
     */
    public function value($value)
    {
        $this->value = $value;
        return $this;
    }
}
