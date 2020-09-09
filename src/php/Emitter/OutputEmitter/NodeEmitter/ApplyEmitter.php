<?php

declare(strict_types=1);

namespace Phel\Emitter\OutputEmitter\NodeEmitter;

use Phel\Ast\ApplyNode;
use Phel\Ast\Node;
use Phel\Ast\PhpVarNode;
use Phel\Emitter\OutputEmitter\NodeEmitter;

final class ApplyEmitter implements NodeEmitter
{
    use WithOutputEmitter;

    public function emit(Node $node): void
    {
        assert($node instanceof ApplyNode);
        $this->outputEmitter->emitContextPrefix($node->getEnv(), $node->getStartSourceLocation());

        $fnNode = $node->getFn();

        if ($fnNode instanceof PhpVarNode && $fnNode->isInfix()) {
            $this->phpVarNodeAndFnNodeIsInfix($node, $fnNode);
        } elseif ($fnNode instanceof PhpVarNode) {
            $this->phpVarNodeButNoInfix($node, $fnNode);
        } else {
            $this->noPhpVarNode($node);
        }

        $this->outputEmitter->emitContextSuffix($node->getEnv(), $node->getStartSourceLocation());
    }

    private function phpVarNodeAndFnNodeIsInfix(ApplyNode $node, PhpVarNode $fnNode): void
    {
        $this->outputEmitter->emitStr('array_reduce([', $node->getStartSourceLocation());
        $this->emitArguments($node);
        $this->outputEmitter->emitStr('], function($a, $b) { return ($a ', $node->getStartSourceLocation());
        $this->outputEmitter->emitStr($fnNode->getName(), $fnNode->getStartSourceLocation());
        $this->outputEmitter->emitStr(' $b); })', $node->getStartSourceLocation());
    }

    private function emitArguments(ApplyNode $node): void
    {
        $argCount = count($node->getArguments());
        foreach ($node->getArguments() as $i => $arg) {
            if ($i < $argCount - 1) {
                $this->outputEmitter->emitNode($arg);
                $this->outputEmitter->emitStr(', ', $node->getStartSourceLocation());
            } else {
                $this->outputEmitter->emitStr('...((', $node->getStartSourceLocation());
                $this->outputEmitter->emitNode($arg);
                $this->outputEmitter->emitStr(') ?? [])', $node->getStartSourceLocation());
            }
        }
    }

    private function phpVarNodeButNoInfix(ApplyNode $node, PhpVarNode $fnNode): void
    {
        $this->outputEmitter->emitStr($fnNode->getName(), $fnNode->getStartSourceLocation());
        $this->outputEmitter->emitStr('(', $node->getStartSourceLocation());
        $this->emitArguments($node);
        $this->outputEmitter->emitStr(')', $node->getStartSourceLocation());
    }

    private function noPhpVarNode(ApplyNode $node): void
    {
        $this->outputEmitter->emitStr('(', $node->getStartSourceLocation());
        $this->outputEmitter->emitNode($node->getFn());
        $this->outputEmitter->emitStr(')', $node->getStartSourceLocation());
        $this->outputEmitter->emitStr('(', $node->getStartSourceLocation());
        $this->emitArguments($node);
        $this->outputEmitter->emitStr(')', $node->getStartSourceLocation());
    }
}