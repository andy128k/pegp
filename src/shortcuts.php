<?php

final class Pegp
{
    public static function str($str)
    {
        return new PegpString($str);
    }

    public static function stri($str)
    {
        return new PegpString($str, true);
    }

    public static function re($expr, $flags='', $delimiter='#')
    {
        return new PegpRegex($expr, $flags, $delimiter);
    }

    public static function seq()
    {
        return new PegpSequence(func_get_args());
    }

    public static function oneOf()
    {
        return new PegpOneOf(func_get_args());
    }

    public static function optional($parser, $emptyValue='')
    {
        return new PegpOptional($parser, $emptyValue);
    }
}

