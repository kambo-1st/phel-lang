<?php

declare(strict_types=1);

namespace PhelTest\Unit\Compiler\Analyzer\TupleSymbol\Binding\Deconstructor;

use Phel\Compiler\Analyzer\TupleSymbol\Binding\BindingValidatorInterface;
use Phel\Compiler\Analyzer\TupleSymbol\Binding\Deconstructor\PhelArrayBindingDeconstructor;
use Phel\Compiler\Analyzer\TupleSymbol\Binding\TupleDeconstructor;
use Phel\Lang\PhelArray;
use Phel\Lang\Symbol;
use Phel\Lang\Tuple;
use PHPUnit\Framework\TestCase;

final class PhelArrayBindingDeconstructorTest extends TestCase
{
    private const EXAMPLE_INDEX = 'index';
    private const EXAMPLE_VALUE = 'example value';

    private PhelArrayBindingDeconstructor $deconstructor;

    public function setUp(): void
    {
        Symbol::resetGen();

        $this->deconstructor = new PhelArrayBindingDeconstructor(
            new TupleDeconstructor(
                $this->createMock(BindingValidatorInterface::class)
            )
        );
    }

    public function testDeconstructSymbol(): void
    {
        $bindings = [];

        $binding = PhelArray::create(
            self::EXAMPLE_INDEX,
            Symbol::create(self::EXAMPLE_VALUE),
        );

        $this->deconstructor->deconstruct($bindings, $binding, self::EXAMPLE_VALUE);

        self::assertEquals([
            [
                Symbol::create('__phel_1'),
                self::EXAMPLE_VALUE,
            ],
            [
                Symbol::create('__phel_2'),
                Tuple::create(
                    Symbol::create(Symbol::NAME_PHP_ARRAY_GET),
                    Symbol::create('__phel_1'),
                    self::EXAMPLE_INDEX
                ),
            ],
            [
                Symbol::create(self::EXAMPLE_VALUE),
                Symbol::create('__phel_2'),
            ],
        ], $bindings);
    }

    public function testDeconstructTuple(): void
    {
        $bindings = [];

        $binding = PhelArray::create(
            self::EXAMPLE_INDEX,
            Tuple::create(),
        );

        $this->deconstructor->deconstruct($bindings, $binding, self::EXAMPLE_VALUE);

        self::assertEquals([
            [
                Symbol::create('__phel_1'),
                self::EXAMPLE_VALUE,
            ],
            [
                Symbol::create('__phel_2'),
                Tuple::create(
                    Symbol::create(Symbol::NAME_PHP_ARRAY_GET),
                    Symbol::create('__phel_1'),
                    self::EXAMPLE_INDEX
                ),
            ],
            [
                Symbol::create('__phel_3'),
                Symbol::create('__phel_2'),
            ],
        ], $bindings);
    }
}
