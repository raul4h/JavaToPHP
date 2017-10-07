<?php

class Token
{

    public $type;
    public $str;
    public $val;

    const __default = self::INVALID;
    const LPAREN = 1;
    const RPAREN = 2;
    const LBRACKET = 3;
    const RBRACKET = 4;
    const LESS = 5;
    const EQUAL = 6;
    const COLON = 7;
    const ID = 8;
    const VALUE = 9;
    const __IF = 10;
    const __ELSE = 11;
    const EOF = 12;
    const INVALID = 13;

    function __construct()
    {
        $arguments = func_get_args();
        $numArgs = func_num_args();

        switch ($numArgs) {
            case 1:
                $this->Token($arguments[0]);
                break;
            case 2:
                $this->Token2($arguments[0], $arguments[1]);
                break;
            case 3:
                $this->Token3($arguments[0], $arguments[1], $arguments[2]);
        }
    }

    function Token($theType)
    {
        $this->type = $theType;
    }

    function Token2($theType, $theString)
    {
        $this->type = $theType;
        $this->str = $theString;
    }

    function Token3($theType, $theString, $theVal)
    {
        $this->type = $theType;
        $this->str = $theString;
        $this->val = $theVal;
    }
}

?>