<?php

namespace Phlexy\Lexer;

class Simple implements \Phlexy\Lexer {
    protected $regexToToken;

    public function __construct(array $regexToToken) {
        $this->regexToToken = array();
        foreach ($regexToToken as $regex => $token) {
            $this->regexToToken['~' . str_replace('~', '\~', $regex) . '~A'] = $token;
        }
    }

    public function lex($string) {
        $tokens = array();

        $offset = 0;
        $line = 1;
        while (isset($string[$offset])) {
            foreach ($this->regexToToken as $regex => $token) {
                if (!preg_match($regex, $string, $matches, 0, $offset)) {
                    continue;
                }

                $tokens[] = array_merge(array($token, $line), $matches);

                $offset += strlen($matches[0]);
                $line += substr_count("\n", $matches[0]);

                continue 2;
            }

            throw new \Phlexy\LexingException(sprintf('Unexpected character "%s"', $string[$offset]));
        }

        return $tokens;
    }
}