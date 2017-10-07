<?php

include "Lexer.php";

static $letters = "abcdefghijklmnopqrstuvwxyz";
static $digits = "0123456789";
static $values = array();
static $oneIndent = "   ";
global $currentToken;
global $lex;

main();

function main()
{
    global $currentToken, $lex, $oneIndent;
    $header = "<html>\n" . "  <head>\n" . "    <title>Program Evaluator</title>\n" . "  </head>\n" . "  <body>\n" . "  <pre>";
    echo $header . "\n";
    $programsURL = "http://cs5339.cs.utep.edu/longpre/assignment2/programs.txt";

    try {
        $inp = fopen($programsURL, 'r');
        $programsInputLine;

        while (($programsInputLine = fgets($inp)) != null){
            $inputUrl = trim($programsInputLine);
            echo $inputUrl."\n";
            $program = "";

            try {
                $in = fopen($inputUrl, 'r');
                $inputLine = "";

                while (($inputLine = fgets($in)) != null) {
                    $program .= " " . trim($inputLine);
                }

            } catch (Exception $e) {
                echo "Unexpected Exception\n";
            }

            $lex = new Lexer($program);
            $currentToken = $lex->next();

            try {
                execProg($oneIndent);

                if ($currentToken->type != Token::EOF) {
                    echo "Unexpected characters at the end of the program\n";
                    throw new Exception();
                }

            } catch (Exception $e) {
                echo "<br/>Program parsing aborted";
            }
            echo "\n";
        }
    } catch (Exception $e) {
        echo "Unexpected Exception \n";
    }
    $footer = "  </pre>\n  </body>\n</html>";
    echo $footer;
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

    $c = substr($currentToken->str,0,1);
    $currentToken = $lex->next();

    if ($currentToken->type != Token::EQUAL){
        echo "/n equal sign expected\n";
        throw new Exception();
    }

    $currentToken = $lex->next();
    echo $indent . $c . " = ";
    $value = execExpr($indent);
    echo "\n";

    if ($executing){
        $values[$c] = $value;
    }
}
function execConditional($indent, $executing){
    global $currentToken,$lex, $oneIndent;

    echo $indent . "if ";
    $currentToken = $lex->next();
    $condResult = execCond($indent);
    echo " {\n";

    if ($currentToken->type != Token::LBRACKET){
        echo "Left bracket expected\n";
        throw new Exception();
    }
    $currentToken = $lex->next();

    while ($currentToken->type == Token::ID || $currentToken->type == Token::__IF){
        execStatement($indent . $oneIndent, $condResult);
    }

    if ($currentToken->type != Token::RBRACKET){
        echo "Right bracket or statement expected\n";
        throw new Exception();
    }

    echo $indent . "}";
    $currentToken = $lex->next();

    if ($currentToken->type == Token::__ELSE){
        $currentToken = $lex->next();

        if ($currentToken->type != Token::LBRACKET){
            echo "Left bracket expected\n";
            throw new Exception();
        }

        $currentToken = $lex->next();
        echo " else {\n";

        while ($currentToken->type == Token::ID || $currentToken->type == Token::__IF){
            execStatement($indent . $oneIndent, !$condResult);
        }

        if ($currentToken->type != Token::RBRACKET){
            echo "Right bracket or statement expected\n";
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
        echo "Left parenthesis expected\n";
        throw new Exception();
    }
    echo "(";
    $currentToken = $lex->next();
    $v1 = execExpr($indent);

    if ($currentToken->type != Token::LESS){
        echo "LESS THAN expected\n";
        throw new Exception();
    }

    echo "&lt;";
    $currentToken = $lex->next();
    $v2 = execExpr($indent);

    if ($currentToken->type != Token::RPAREN){
        echo "Right parenthesis expected\n";
        throw new Exception();
    }
    echo ")";
    $currentToken = $lex->next();
    return $v1 < $v2;
}

function execExpr($indent){
    global $currentToken,$lex,$values;

    if ($currentToken->type == Token::VALUE){
        $val = $currentToken->val;
        echo $val;
        $currentToken = $lex->next();
        return $val;
    }

    if ($currentToken->type == Token::ID){
        $c = substr($currentToken->str,0,1);
        echo $c;

        if (array_key_exists($c,$values)){
            $currentToken = $lex->next();
            return intval($values[$c]);
        }

        else{
            echo "Reference to an undefined variable\n";
            throw new Exception();
        }
    }
    echo "An expression should be either a digit or a letter\n";
    throw new Exception();
}

function execResults($indent){
    global $currentToken,$lex, $values;

    if ($currentToken->type != Token::COLON){
        echo "COLON or statement expected\n";
        throw new Exception();
    }

    $currentToken = $lex->next();

    while ($currentToken->type == Token::ID){
        $c = substr($currentToken->str,0,1);
        $currentToken = $lex->next();

        if (array_key_exists($c,$values)){
            echo "The value of " . $c . " is " . $values[$c] . "\n";
        }
        else{
            echo "The value of " . $c . " is undefined\n";
        }
    }
}
?>