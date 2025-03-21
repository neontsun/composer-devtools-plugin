<?php

declare(strict_types = 1);

namespace Neontsun\Composer\Devtools\Tests;

use const PHP_EOL;

use Composer\IO\BufferIO;
use Neontsun\Composer\Devtools\Logger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

#[CoversClass(Logger::class)]
final class LoggerTest extends TestCase
{
    private const VERBOSITIES = [
        OutputInterface::VERBOSITY_QUIET,
        OutputInterface::VERBOSITY_NORMAL,
        OutputInterface::VERBOSITY_VERBOSE,
        OutputInterface::VERBOSITY_VERY_VERBOSE,
        OutputInterface::VERBOSITY_DEBUG,
    ];

    /**
     * @param non-empty-string $message
     * @throws ExpectationFailedException
     * @throws RuntimeException
     */
    #[Test]
    #[DataProvider('messageProvider')]
    public function successLogMessage(int $verbosity, string $message, string $expected): void
    {
        $bufferIO = new BufferIO(input: '', verbosity: $verbosity);
        $logger = new Logger($bufferIO);

        $logger->info($message);

        self::assertSame($expected, $bufferIO->getOutput());
    }

    /**
     * @param non-empty-string $message
     * @throws ExpectationFailedException
     * @throws RuntimeException
     */
    #[Test]
    #[DataProvider('messageDebugProvider')]
    public function successLogDebugMessage(int $verbosity, string $message, string $expected): void
    {
        $bufferIO = new BufferIO(input: '', verbosity: $verbosity);
        $logger = new Logger($bufferIO);

        $logger->debug($message);

        self::assertSame($expected, $bufferIO->getOutput());
    }

    /**
     * @return iterable<array{0: positive-int, 1: non-empty-string, 2: string}>
     */
    public static function messageProvider(): iterable
    {
        $notLoggedVerbosities = [
            OutputInterface::VERBOSITY_QUIET,
        ];

        $loggedVerbosities = array_diff(
            self::VERBOSITIES,
            $notLoggedVerbosities,
        );

        $message = 'Hello, Foo!';
        $expected = '[devtools] Hello, Foo!' . PHP_EOL;

        foreach ($notLoggedVerbosities as $verbosity) {
            yield [$verbosity, $message, ''];
        }

        foreach ($loggedVerbosities as $verbosity) {
            yield [$verbosity, $message, $expected];
        }
    }

    /**
     * @return iterable<array{0: positive-int, 1: non-empty-string, 2: string}>
     */
    public static function messageDebugProvider(): iterable
    {
        $notLoggedVerbosities = [
            OutputInterface::VERBOSITY_QUIET,
            OutputInterface::VERBOSITY_NORMAL,
        ];

        $loggedVerbosities = array_diff(
            self::VERBOSITIES,
            $notLoggedVerbosities,
        );

        $message = 'Hello, Foo!';
        $expected = '[devtools] Hello, Foo!' . PHP_EOL;

        foreach ($notLoggedVerbosities as $verbosity) {
            yield [$verbosity, $message, ''];
        }

        foreach ($loggedVerbosities as $verbosity) {
            yield [$verbosity, $message, $expected];
        }
    }
}
