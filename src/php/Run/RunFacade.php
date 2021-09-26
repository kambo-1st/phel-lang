<?php

declare(strict_types=1);

namespace Phel\Run;

use Gacela\Framework\AbstractFacade;
use Phel\Run\Command\Repl\ReplCommand;
use Phel\Run\Command\Run\RunCommand;
use Phel\Run\Command\Test\TestCommand;

/**
 * @method RunFactory getFactory()
 */
final class RunFacade extends AbstractFacade implements RunFacadeInterface
{
    public function getReplCommand(): ReplCommand
    {
        return $this->getFactory()->createReplCommand();
    }

    public function getRunCommand(): RunCommand
    {
        return $this->getFactory()->createRunCommand();
    }

    public function getTestCommand(): TestCommand
    {
        return $this->getFactory()->createTestCommand();
    }
}
