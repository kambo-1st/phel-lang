<?php

declare(strict_types=1);

namespace PhelTest\Integration\Command\Format;

use Gacela\Framework\Gacela;
use Phel\Command\Format\FormatCommand;
use PhelTest\Integration\Command\AbstractCommandTest;
use Symfony\Component\Console\Input\InputInterface;

final class FormatCommandTest extends AbstractCommandTest
{
    private const FIXTURES_DIR = __DIR__ . '/Fixtures/';

    public static function setUpBeforeClass(): void
    {
        Gacela::bootstrap(__DIR__);
    }

    public function test_good_format(): void
    {
        $path = self::FIXTURES_DIR . 'good-format.phel';
        $oldContent = file_get_contents($path);

        $command = $this->getFormatCommand();

        $this->expectOutputRegex('/No files were formatted+/s');

        try {
            $command->run(
                $this->stubInput([$path]),
                $this->stubOutput()
            );
        } finally {
            file_put_contents($path, $oldContent);
        }
    }

    public function test_bad_format(): void
    {
        $path = self::FIXTURES_DIR . 'bad-format.phel';
        $oldContent = file_get_contents($path);

        $command = $this->getFormatCommand();

        $this->expectOutputString(<<<TXT
Formatted files:
  1) $path

TXT);
        try {
            $command->run(
                $this->stubInput([$path]),
                $this->stubOutput()
            );
        } finally {
            file_put_contents($path, $oldContent);
        }
    }

    private function getFormatCommand(): FormatCommand
    {
        return $this->createCommandFacade()->getFormatCommand();
    }

    private function stubInput(array $paths): InputInterface
    {
        $input = $this->createStub(InputInterface::class);
        $input->method('getArgument')->willReturn($paths);

        return $input;
    }
}
