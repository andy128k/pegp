<?php

namespace Pegp\Parser;

use Pegp\Input;
use Pegp\Result;

class OneOf extends Parser
{
    /** @var Parser[] */
    private $parsers;

    /**
     * @param Parser[] $parsers
     */
    public function __construct(array $parsers)
    {
        $this->parsers = $parsers;

        $comment = array();
        foreach ($this->parsers as $parser) {
            $comment[] = $parser->getComment();
        }
        $this->setComment('one of: '.implode(', ', $comment));
    }

    /**
     * @param Input $input
     * @return Result
     */
    public function doParse(Input $input)
    {
        foreach ($this->parsers as $parser) {
            $r = $parser->parse($input);
            if ($r->isSuccess()) {
                return $r;
            }
        }
        return Result::failure($input, $this);
    }
}
