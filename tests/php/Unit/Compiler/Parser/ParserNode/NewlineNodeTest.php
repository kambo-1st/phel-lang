<?php

declare(strict_types=1);

namespace PhelTest\Unit\Compiler\Parser\ParserNode;

use Phel\Compiler\Parser\ParserNode\NewlineNode;
use Phel\Lang\SourceLocation;
use PHPUnit\Framework\TestCase;

final class NewlineNodeTest extends TestCase
{
    public function testGetCode(): void
    {
        self::assertEquals(
            '\n',
            (new NewlineNode('\n', $this->loc(1, 0), $this->loc(2, 0)))->getCode()
        );
    }

    public function testGetStartLocation(): void
    {
        self::assertEquals(
            $this->loc(1, 0),
            (new NewlineNode('\n', $this->loc(1, 0), $this->loc(2, 0)))->getStartLocation()
        );
    }

    public function testGetEndLocation(): void
    {
        self::assertEquals(
            $this->loc(2, 0),
            (new NewlineNode('\n', $this->loc(1, 0), $this->loc(2, 0)))->getEndLocation()
        );
    }

    private function loc(int $line, int $column): SourceLocation
    {
        return new SourceLocation('string', $line, $column);
    }
}