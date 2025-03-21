<?php

declare(strict_types = 1);

namespace Neontsun\Composer\Devtools\Tests\Config;

use Neontsun\Composer\Devtools\Config\Config;
use Neontsun\Composer\Devtools\Exception\InvalidComposerExtraConfigException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

use function sprintf;

#[CoversClass(Config::class)]
final class ConfigTest extends TestCase
{
    /**
     * @param array<array-key, mixed> $extra
     * @throws InvalidComposerExtraConfigException
     * @throws ExpectationFailedException
     */
    #[Test]
    #[DataProvider('configProvider')]
    public function successInstantiatedConfig(array $extra, string $expectedRepository, bool $expectedRepositoryEmpty): void
    {
        $config = new Config($extra);

        self::assertSame($expectedRepository, $config->getRepository());
        self::assertSame($expectedRepositoryEmpty, $config->repositoryIsEmpty());
    }

    /**
     * @param array<array-key, mixed> $extra
     * @param non-empty-string $message
     * @throws InvalidComposerExtraConfigException
     */
    #[Test]
    #[DataProvider('invalidConfigProvider')]
    public function cannotBeInstantiatedWithInvalidConfig(array $extra, string $message): void
    {
        $this->expectException(InvalidComposerExtraConfigException::class);
        $this->expectExceptionMessage($message);

        new Config($extra);
    }

    /**
     * @return iterable<non-empty-string, array{0: array<array-key, mixed>, 1: string}>
     */
    public static function configProvider(): iterable
    {
        yield 'default value' => [
            [],
            '',
            true,
        ];

        yield 'empty extra key' => [
            [
                'unknown' => [],
            ],
            '',
            true,
        ];

        yield 'as default but explicit' => [
            [
                Config::EXTRA_CONFIG_KEY => [
                    Config::REPOSITORY => '',
                ],
            ],
            '',
            true,
        ];

        yield 'explicit' => [
            [
                Config::EXTRA_CONFIG_KEY => [
                    Config::REPOSITORY => 'https://github.com/neontsun/composer-devtools-plugin',
                ],
            ],
            'https://github.com/neontsun/composer-devtools-plugin',
            false,
        ];
    }

    /**
     * @return iterable<non-empty-string, array{0: array<array-key, mixed>, 1: non-empty-string}>
     */
    public static function invalidConfigProvider(): iterable
    {
        yield 'non array extra config key' => [
            [
                Config::EXTRA_CONFIG_KEY => 'foo',
            ],
            sprintf(
                'Expected setting "extra.%s" to be a array value. Got "string".',
                Config::EXTRA_CONFIG_KEY,
            ),
        ];

        yield 'non string repository' => [
            [
                Config::EXTRA_CONFIG_KEY => [
                    Config::REPOSITORY => 123,
                ],
            ],
            sprintf(
                'Expected setting "extra.%s.%s" to be a string value. Got "int".',
                Config::EXTRA_CONFIG_KEY,
                Config::REPOSITORY,
            ),
        ];
    }
}
