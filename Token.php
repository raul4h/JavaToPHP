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

    function __construct($theType)
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