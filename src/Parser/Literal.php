<?php

namespace Pegp\Parser;

use Pegp\Input;
use Pegp\Result;

class Literal extends Parser
{
    /** @var string */
    private $str;

    /** @var bool */
    private $caseInsensitive;

    /**
     * @param string $str
     * @param bool $caseInsensitive
     */
    public function __construct($str, $caseInsensitive = false)
    {
        $this->str = $str;
        $this->caseInsensitive = $caseInsensitive;
        $this->setComment('<literal ' . $this->str . '>');
    }

    /**
     * @param Input $input
     * @return Result
     */
    public function doParse(Input $input)
    {
        $len = strlen($this->str);
        if (0 == substr_compare($input->data, $this->str, $input->pos, $len, $this->caseInsensitive)) { // ?? len
            return Result::success(
                $input->advance($len),
                $this->str
            );
        } else {
            return Result::failure($input, $this);
        }
    }
}
