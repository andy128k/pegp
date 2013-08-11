<?php

class PegpResult
{
    private $success;
    public $input, $result;

    public function __construct($success, PegpInput $input, $result=null)
    {
        $this->success = $success;
        $this->input = $input;
        $this->result = $result;
    }

    public static function success(PegpInput $input, $result=null)
    {
        return new PegpResult(true, $input, $result);
    }

    public static function failure(PegpInput $input, $parser=null)
    {
        return new PegpResult(false, $input, $parser);
    }

    public function isSuccess()
    {
        return $this->success;
    }
}

