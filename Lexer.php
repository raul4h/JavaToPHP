<?php
include "Token.php";

class Lexer
{


    static $letters = "abcdefghijklmnopqrstuvwxyz";
    static $digits = "0123456789";

    private $prog;
    private $i;

    function __construct($s)
    {
        $this->prog = str_split($s);
        $this->i = 0;
    }

    public function next()
    {
        while ($this->i < count($this->prog) && ($this->prog[$this->i] == ' ' || $this->prog[$this->i] == '\n')) {
            $this->i += 1;
        }
        if ($this->i >= count($this->prog)) {
            return new Token(Token::EOF);
        }
        switch ($this->prog[$this->i]) {
            case '(':
                $this->i += 1;
                return new Token2(Token::LPAREN, "(");
            case ')':
                $this->i += 1;
                return new Token2(Token::RPAREN, ")");
            case '{':
                $this->i += 1;
                return new Token2(Token::LBRACKET, "{");
            case '}':
                $this->i += 1;
                return new Token2(Token::RBRACKET, "}");
            case '<':
                $this->i += 1;
                return new Token2(Token::LESS, "<");
            case '=':
                $this->i += 1;
                return new Token2(Token::EQUAL, "=");
            case ':':
                $this->i += 1;
                return new Token2(Token::COLON, ":");
        }
        if (strpos(Lexer::$digits, $this->prog[$this->i]) != -1) {
            $digit = $this->prog[$this->i];
            $this->i += 1;
            return new Token3(Token::VALUE, "" . $digit, intval($digit));
        }
        if (strpos(Lexer::$letters, $this->prog[$this->i] != -1)) {
            $id = "";
            while ($this->i < count($this->prog) && strpos(Lexer::$letters, $this->prog[$this->i]) != -1) {
                $id += $this->prog[$this->i];
                $this->i += 1;
            }
            if ("if" == $id) {
                return new Token2(Token::__IF, $id);
            }
            if ("else" == $id) {
                return new Token2(Token::__ELSE, $id);
            }
            if (count(str_split($id)) == 1) {
                return new Token2(Token::ID, $id);
            }
            return new Token2(Token::INVALID, "");
        }
        return new Token2(Token::INVALID, "");
    }

}

?>