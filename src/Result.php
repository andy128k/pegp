<?php

namespace Pegp;

class Result
{
    /** @var boolean */
    private $success;

    /** @var Input */
    public $input;

    /** @var mixed|Parser */
    public $result;

    /**
     * @param boolean $success
     * @param Input $input
     * @param mixed|Parser $result
     */
    public function __construct($success, Input $input, $result=null)
    {
        $this->success = $success;
        $this->input = $input;
        $this->result = $result;
    }

    /**
     * @param Input $input
     * @param mixed $result
     * @return Result
     */
    public static function success(Input $input, $result=null)
    {
        return new self(true, $input, $result);
    }

    /**
     * @param Input $input
     * @param Parser $parser
     * @return Result
     */
    public static function failure(Input $input, $parser=null)
    {
        return new self(false, $input, $parser);
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }
}
