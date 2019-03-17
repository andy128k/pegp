<?php

namespace Pegp;

use Pegp\Parser\Parser;
use Pegp\Parser\OneOf;
use Pegp\Parser\Optional;
use Pegp\Parser\Regex;
use Pegp\Parser\Sequence;
use Pegp\Parser\Literal;

final class Pegp
{
    /**
     * @param string $str
     * @return Literal
     */
    public static function str($str)
    {
        return new Literal($str);
    }

    /**
     * @param string $str
     * @return Literal
     */
    public static function stri($str)
    {
        return new Literal($str, true);
    }

    /**
     * @param string $expr
     * @param string $flags
     * @param string $delimiter
     * @return Regex
     */
    public static function re($expr, $flags = '', $delimiter = '#')
    {
        return new Regex($expr, $flags, $delimiter);
    }

    /**
     * @param Parser[] ...$parsers
     * @return Sequence
     */
    public static function seq(...$parsers)
    {
        return new Sequence($parsers);
    }

    /**
     * @param Parser[] ...$parsers
     * @return OneOf
     */
    public static function oneOf(...$parsers)
    {
        return new OneOf($parsers);
    }

    /**
     * @param Parser $parser
     * @param string $emptyValue
     * @return Optional
     */
    public static function optional($parser, $emptyValue = '')
    {
        return new Optional($parser, $emptyValue);
    }
}
