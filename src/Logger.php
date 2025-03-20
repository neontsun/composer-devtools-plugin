<?php

declare(strict_types = 1);

namespace Neontsun\Composer\Devtools;

use Composer\IO\IOInterface;

use function sprintf;

final readonly class Logger
{
    public function __construct(
        private IOInterface $io,
    ) {}

    /**
     * @param non-empty-string $message
     */
    public function info(string $message): void
    {
        $this->log($message);
    }

    /**
     * @param non-empty-string $message
     */
    public function debug(string $message): void
    {
        $this->log($message, true);
    }

    /**
     * @param non-empty-string $message
     */
    private function log(string $message, bool $isDebug = false): void
    {
        $verbosity = $isDebug
        	? IOInterface::VERBOSE
        	: IOInterface::NORMAL;

        $this->io->write(sprintf('[devtools] %s', $message), true, $verbosity);
    }
}
