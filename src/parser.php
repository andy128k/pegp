<?php

abstract class PegpParser
{
    public $comment = null;
    private $drop = false;
    private $value = null;

    public function parseString($string)
    {
        $input = new PegpInput($string);
        $result = $this->parse($input);
        if (!$result->isSuccess())
            throw new Exception('Parse failed. Expected ' . $result->result->getComment() . ' at ' . $result->input->pos);
        if ($input->length != $result->input->pos)
            throw new Exception('Parse failed. Unparsed tail at ' . $result->input->pos);
        return $result->result;
    }

    public function parse(PegpInput $input)
    {
        $result = $this->doParse($input);
        if ($result->isSuccess()) {
            return $this->process($result);
        } else {
            return $result;
        }
    }

    public abstract function doParse(PegpInput $input);

    protected function process($result)
    {
        if ($this->drop) {
            return PegpResult::success($result->input, null);
        } elseif ($this->value !== null) {
            return PegpResult::success($result->input, $this->value);
        } else {
            return $result;
        }
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    public function drop()
    {
        $this->drop = true;
        return $this;
    }

    public function value($value)
    {
        $this->value = $value;
        return $this;
    }
}

class PegpString extends PegpParser
{
    private $str, $caseInsensitive;

    public function __construct($str, $caseInsensitive=false)
    {
        $this->str = $str;
        $this->caseInsensitive = $caseInsensitive;
    }

    public function doParse(PegpInput $input)
    {
        $len = strlen($this->str);
        if (0 == substr_compare($input->data, $this->str, $input->pos, $len, $this->caseInsensitive)) { // ?? len
            return PegpResult::success(
                $input->advance($len),
                $this->str
            );
        } else {
            return PegpResult::failure($input, $this);
        }
    }

    public function getComment()
    {
        if ($this->comment !== null)
            return $this->comment;
        else
            return '<string '.$this->str.'>';
    }
}

class PegpRegex extends PegpParser
{
    private $expr;

    public function __construct($expr, $flags='', $delimiter='#')
    {
        $this->expr = $delimiter . '^' . $expr . $delimiter . $flags;
    }

    public function doParse(PegpInput $input)
    {
        if (preg_match($this->expr, $input->pick(), $m)) {
            return PegpResult::success(
                $input->advance(strlen($m[0])),
                $m
            );
        } else {
            return PegpResult::failure($input, $this);
        }
    }

    public function getComment()
    {
        if ($this->comment !== null)
            return $this->comment;
        else
            return '<regexp '.$this->expr.'>';
    }
}

class PegpSequence extends PegpParser
{
    private $parsers, $op = null;

    public function __construct(array $parsers)
    {
        $this->parsers = $parsers;
    }

    public function doParse(PegpInput $input)
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
                return PegpResult::failure($inp, $parser);
            }
        }
        return PegpResult::success($inp, $result);
    }

    protected function process($result)
    {
        switch ($this->op) {
            case 'join':
                $result = PegpResult::success($result->input, implode('', $result->result));
                break;
            case 'bitOr':
                $value = 0;
                foreach ($result->result as $v)
                    $value |= $v;
                $result = PegpResult::success($result->input, $value);
                break;
            case 'sum':
                $value = 0;
                foreach ($result->result as $v)
                    $value += $v;
                $result = PegpResult::success($result->input, $value);
                break;
            case 'product':
                $value = 1;
                foreach ($result->result as $v)
                    $value *= $v;
                $result = PegpResult::success($result->input, $value);
                break;
        }
        return parent::process($result);
    }

    public function join()
    {
        $this->op = 'join';
        return $this;
    }

    public function bitOr()
    {
        $this->op = 'bitOr';
        return $this;
    }

    public function sum()
    {
        $this->op = 'sum';
        return $this;
    }

    public function product()
    {
        $this->op = 'product';
        return $this;
    }
}

class PegpOneOf extends PegpParser
{
    private $parsers;

    public function __construct(array $parsers)
    {
        $this->parsers = $parsers;
    }

    public function doParse(PegpInput $input)
    {
        foreach ($this->parsers as $parser) {
            $r = $parser->parse($input);
            if ($r->isSuccess()) {
                return $r;
            }
        }
        return PegpResult::failure($input, $this);
    }

    public function getComment()
    {
        if ($this->comment !== null)
            return $this->comment;
        $comment = array();
        foreach ($this->parsers as $parser) {
            $comment[] = $parser->getComment();
        }
        return 'one of: '.implode(', ', $comment);
    }
}

class PegpOptional extends PegpParser
{
    private $parser, $emptyValue;

    public function __construct(PegpParser $parser, $emptyValue='')
    {
        $this->parser = $parser;
        $this->emptyValue = $emptyValue;
    }

    public function doParse(PegpInput $input)
    {
        $r = $this->parser->parse($input);
        if ($r->isSuccess()) {
            return $r;
        } else {
            return PegpResult::success($input, $this->emptyValue);
        }
    }
}

