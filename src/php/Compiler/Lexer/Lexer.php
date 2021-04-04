<?php

declare(strict_types=1);

namespace Phel\Compiler\Lexer;

use Generator;
use Phel\Compiler\Lexer\Exceptions\LexerValueException;
use Phel\Lang\SourceLocation;

final class Lexer implements LexerInterface
{
    private const REGEXPS = [
        "([ \t]+)", // Whitespace (index: 2)
        "(\r?\n)", // Newline (index: 3)
        "(\#[^\n]*\n?)", // Comment (index: 4)
        '(,@)', // unquote-splicing (index: 5)
        "(\()", // open parenthesis (index: 6)
        "(\))", // close parenthesis (index: 7)
        "(\[)", // open bracket (index: 8)
        "(\])", // close bracket (index: 9)
        "(\{)", // open brace (index: 10)
        "(\})", // close brace (index: 11)
        "(')", // quote (index: 12)
        '(,)', // unquote (index: 13)
        '(`)', // quasiquote (index: 14)
        "(\^)", // caret (index: 15)
        "(@\[)", // array (index: 16)
        "(@\{)", // table (index: 17)
        "(\|\()", // short fn (index: 18)
        '("(?:[^"\\\\]++|\\\\.)*+")', // String (index: 19)
        "([^\(\)\[\]\{\}',`@ \n\r\t\#]+)", // Atom (index: 20)
    ];

    private int $cursor = 0;
    private int $line = 1;
    private int $column = 0;
    private string $combinedRegex;
    private bool $withoutLocation;

    public function __construct(bool $withoutLocation = false)
    {
        $this->combinedRegex = '/(?:' . implode('|', self::REGEXPS) . ')/mA';
        $this->withoutLocation = $withoutLocation;
    }

    /**
     * @throws LexerValueException
     */
    public function lexString(string $code, string $source = self::DEFAULT_SOURCE, int $startingLine = 1): TokenStream
    {
        return new TokenStream($this->lexStringGenerator($code, $source, $startingLine));
    }

    /**
     * @throws LexerValueException
     *
     * @return Generator<Token>
     */
    private function lexStringGenerator(string $code, string $source = self::DEFAULT_SOURCE, int $startingLine = 1): Generator
    {
        $this->cursor = 0;
        $this->line = $startingLine;
        $this->column = 0;
        $end = strlen($code);

        $startLocation = new SourceLocation($source, $this->line, $this->column);
        if ($this->withoutLocation) {
            $startLocation = new SourceLocation('string', 0, 0);
        }

        while ($this->cursor < $end) {
            if (preg_match($this->combinedRegex, $code, $matches, 0, $this->cursor)) {
                $this->moveCursor($matches[0]);
                $endLocation = new SourceLocation($source, $this->line, $this->column);
                if ($this->withoutLocation) {
                    $endLocation = new SourceLocation('string', 0, 0);
                }

                yield new Token(count($matches), $matches[0], $startLocation, $endLocation);

                $startLocation = $endLocation;
            } else {
                throw LexerValueException::unexpectedLexerState();
            }
        }

        yield new Token(Token::T_EOF, '', $startLocation, $startLocation);
    }

    private function moveCursor(string $str): void
    {
        $len = strlen($str);
        $this->cursor += $len;
        $lastNewLinePos = strrpos($str, "\n");

        if ($lastNewLinePos !== false) {
            $this->line += substr_count($str, "\n");
            $this->column = $len - $lastNewLinePos - 1;
        } else {
            $this->column += $len;
        }
    }
}
