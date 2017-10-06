<?php
include "Token.php";
include "Lexer.php";

static $letters = "abcdefghijklmnopqrstuvwxyz";
static $digits = "0123456789";
static $values = array();
static $oneIndent = "   ";
global $currentToken;
global $lex;

function main()
{
    global $currentToken, $lex, $oneIndent;
    $header = "<html>\n" . "  <head>\n" . "    <title>Program Evaluator</title>\n" . "  </head>\n" . "  <body>\n" . "  <pre>";
    echo $header . "\n";
    $programsURL = "http://cs5339.cs.utep.edu/longpre/assignment2/programs.txt";
    try {
        $imp = fopen($programsURL, 'r');
        $programsInputLine = "";
        while ($programsInputLine = fgets($imp) != null) {
            echo $programsInputLine . "\n";
            $inputURL = $programsInputLine;
            $program = "";
            try {
                $in = fopen($programsURL, 'r');
                $inputLine = "";
                while ($inputLine = fgets($in) != null) {
                    $program .= '\n' . $inputLine;
                }
            } catch (Exception $e) {
                echo "Unexpected Exception\n";
            }
            $lex = new Lexer($program);
            $currentToken = $lex->next();
            try {
                $this->execProg($oneIndent);
                if ($currentToken->type != Token::EOF) {
                    echo "Unexpected characters at the end of the program \n";
                    throw new Exception();
                }
            } catch (Exception $e) {
                echo "<br/>Program parsing aborted \n";
            }
            echo "\n";
        }
    } catch (Exception $e) {
        echo "Unexpected Exception \n";
    }
    $footer = "  </pre>\n  </body>\n\" + \"</html>";
    echo $footer . "\n";
}

function execProg($indent)
{
    global $currentToken;
    while ($currentToken->type == Token::ID || $currentToken->type == Token::__IF) {
        execStatement($indent, true);
    }
    echo "\n";
    execResults($indent);
}

function execStatement($indent, $executing)
{
    global $currentToken;
    if ($currentToken->type == Token::ID) {
        execAssign($indent,$executing);
    }
    else{
        execConditional($indent,$executing);
    }
}
function execAssign($indent,$executing){
    global $currentToken, $lex, $values;
    $c = substr($currentToken->type,0,1);
    $currentToken = $lex->next();
    if ($currentToken->type != Token::EQUAL){
        echo "\n equal sign expected";
        throw new Exception();
    }
    $currentToken = $lex->next();
    echo $indent . $c . " = \n";
    $value = execExpr($indent);
    echo "\n";
    if ($executing){
        $values[$c] = $value;
    }
}
function execConditional($indent, $executing){
    global $currentToken,$lex, $oneIndent;
    echo $indent . "if \n";
    $currentToken = $lex->next();
    $condResult = execCond($indent);
    echo " {\n";
    if ($currentToken->type == Token::LBRACKET){
        echo "Left bracket expected \n";
        throw new Exception();
    }
    $currentToken = $lex->next();
    while ($currentToken->type == Token::ID || $currentToken->type == Token::__IF){
        execStatement($indent . $oneIndent, $condResult);
    }
    if ($currentToken->type != Token::RBRACKET){
        echo "Right bracket or statement expected \n";
        throw new Exception();
    }
    echo $indent . "} \n";
    $currentToken = $lex->next();
    if ($currentToken->type == Token::__ELSE){
        $currentToken = $lex->next();
        if ($currentToken->type != Token::LBRACKET){
            echo "Left bracket expected \n";
            throw new Exception();
        }
        $currentToken = $lex->next();
        echo " else { \n";
        while ($currentToken->type == Token::ID || $currentToken->type == Token::__IF){
            execStatement($indent . $oneIndent, !$condResult);
        }
        if ($currentToken->type != Token::RBRACKET){
            echo "Right bracket or statement expected \n";
            throw new Exception();
        }
        echo $indent . "}";
        $currentToken = $lex->next();
    }
    echo "\n";
}

function execCond($indent){
    global $currentToken, $lex;
    if ($currentToken->type != Token::LPAREN){
        echo "Left parenthesis expected \n";
        throw new Exception();
    }
    echo "(";
    $currentToken = $lex->next();
    $v1 = execExpr($indent);
    if ($currentToken->type != Token::LESS){
        echo "LESS THAN expected \n";
        throw new Exception();
    }
    echo "&lt;";
    $currentToken = $lex->next();
    $v2 = execExpr($indent);
    if ($currentToken->type != Token::RPAREN){
        echo "Right parenthesis expected \n";
        throw new Exception();
    }
    echo ")";
    $currentToken = $lex->next();
    return $v1 < $v2;
}



?>