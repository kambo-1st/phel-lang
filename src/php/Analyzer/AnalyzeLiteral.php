<?php

declare(strict_types=1);

namespace Phel\Analyzer;

use Phel\Ast\LiteralNode;
use Phel\Lang\AbstractType;
use Phel\NodeEnvironment;

final class AnalyzeLiteral
{
    /** @param AbstractType|scalar|null $value */
    public function __invoke($value, NodeEnvironment $env): LiteralNode
    {
        $sourceLocation = ($value instanceof AbstractType) ? $value->getStartLocation() : null;

        return new LiteralNode($env, $value, $sourceLocation);
    }
}
