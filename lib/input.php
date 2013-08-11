<?php

class PegpInput
{
    public $data, $length, $pos;

    public function __construct($data, $length=null, $pos=0)
    {
        $this->data = $data;
        $this->length = $length ? $length : strlen($data);
        $this->pos = $pos;
    }

    public function pick($length=PHP_INT_MAX)
    {
        $length = min($length, $this->length - $this->pos);
        if ($length > 0)
            return substr($this->data, $this->pos, $length);
        else
            return '';
    }

    public function advance($len)
    {
        return new PegpInput($this->data, $this->length, $this->pos + $len);
    }
}

