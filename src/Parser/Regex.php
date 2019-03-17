<?php

namespace Pegp\Parser;

use Pegp\Input;
use Pegp\Result;

class Regex extends Parser
{
    /** @var string */
    private $expr;

    /** @var string|null */
    private $groupName = null;

    /**
     * @param string $expr
     * @param string $flags
     * @param string $delimiter
     */
    public function __construct($expr, $flags='', $delimiter='#')
    {
        $this->expr = $delimiter . '^' . $expr . $delimiter . $flags;
        $this->setComment('<regexp '.$this->expr.'>');
    }

    /**
     * @param Input $input
     * @return Result
     */
    public function doParse(Input $input)
    {
        if (preg_match($this->expr, $input->pick(), $m)) {
            return Result::success(
                $input->advance(strlen($m[0])),
                $m
            );
        } else {
            return Result::failure($input, $this);
        }
    }

    /**
     * @param string $groupName
     * @return Regex
     */
    public function group($groupName)
    {
        $this->groupName = $groupName;
        return $this;
    }

    /**
     * @param Result $result
     * @return Result
     */
    protected function process($result)
    {
        if ($this->groupName !== null) {
            $result = Result::success($result->input, $result->result[$this->groupName]);
        }
        return parent::process($result);
    }
}
