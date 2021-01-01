<?php

declare(strict_types=1);

namespace Phel\Compiler\Emitter\OutputEmitter\NodeEmitter;

use Phel\Compiler\Ast\ArrayNode;
use Phel\Compiler\Ast\AbstractNode;
use Phel\Compiler\Emitter\OutputEmitter\NodeEmitter;

final class ArrayEmitter implements NodeEmitter
{
    use WithOutputEmitterTrait;

    public function emit(AbstractNode $node): void
    {
        assert($node instanceof ArrayNode);

        $this->outputEmitter->emitContextPrefix($node->getEnv(), $node->getStartSourceLocation());
        $this->outputEmitter->emitStr('\Phel\Lang\PhelArray::create(', $node->getStartSourceLocation());
        $this->outputEmitter->emitArgList($node->getValues(), $node->getStartSourceLocation());
        $this->outputEmitter->emitStr(')', $node->getStartSourceLocation());
        $this->outputEmitter->emitContextSuffix($node->getEnv(), $node->getStartSourceLocation());
    }
}
