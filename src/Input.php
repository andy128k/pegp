<?php

namespace Pegp;

final class Input
{
    /** @var string */
    public $data;

    /** @var integer */
    public $length;

    /** @var integer */
    public $pos;

    /**
     * @param string $data
     * @param integer $length
     * @param integer $pos
     */
    public function __construct($data, $length=null, $pos=0)
    {
        $this->data = $data;
        $this->length = $length ? $length : strlen($data);
        $this->pos = $pos;
    }

    /**
     * @param int $length
     * @return string
     */
    public function pick($length=PHP_INT_MAX)
    {
        $length = min($length, $this->length - $this->pos);
        if ($length > 0)
            return substr($this->data, $this->pos, $length);
        else
            return '';
    }

    /**
     * @param integer $len
     * @return Input
     */
    public function advance($len)
    {
        return new self($this->data, $this->length, $this->pos + $len);
    }
}
