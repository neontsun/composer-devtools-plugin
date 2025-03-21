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
    public function successInstantiatedConfig(
		array $extra, 
		string $expectedRepository,
		string $expectedTargetDirectory,
		bool $expectedNeedUpdateGitIgnore,
	): void {
        $config = new Config($extra);

        self::assertSame($expectedRepository, $config->getSourceLink());
        self::assertSame($expectedTargetDirectory, $config->getTargetDirectory());
        self::assertSame($expectedNeedUpdateGitIgnore, $config->needUpdateGitIgnore());
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
        yield 'explicit source link' => [
            [
                Config::EXTRA_CONFIG_KEY => [
                    Config::SOURCE_LINK => 'https://github.com/neontsun/composer-devtools-plugin',
                ],
            ],
            'https://github.com/neontsun/composer-devtools-plugin',
            Config::TARGET_DIRECTORY_DEFAULT,
			Config::UPDATE_GITIGNORE_DEFAULT,
        ];

        yield 'explicit source link and target directory' => [
            [
                Config::EXTRA_CONFIG_KEY => [
                    Config::SOURCE_LINK => 'https://github.com/neontsun/composer-devtools-plugin',
					Config::TARGET_DIRECTORY => 'utils',
                ],
            ],
            'https://github.com/neontsun/composer-devtools-plugin',
            'utils',
			Config::UPDATE_GITIGNORE_DEFAULT,
        ];

        yield 'explicit all' => [
            [
                Config::EXTRA_CONFIG_KEY => [
                    Config::SOURCE_LINK => 'https://github.com/neontsun/composer-devtools-plugin',
					Config::TARGET_DIRECTORY => 'utils',
					Config::UPDATE_GITIGNORE => false,
                ],
            ],
            'https://github.com/neontsun/composer-devtools-plugin',
            'utils',
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
		
		yield 'empty config' => [
			[
				Config::EXTRA_CONFIG_KEY => [],
			],
			sprintf(
				'Expected setting "extra.%s.%s" to be a string value. Got "null".',
				Config::EXTRA_CONFIG_KEY,
				Config::SOURCE_LINK,
			),
		];
		
        yield 'non string source link' => [
            [
                Config::EXTRA_CONFIG_KEY => [
                    Config::SOURCE_LINK => 123,
                ],
            ],
            sprintf(
                'Expected setting "extra.%s.%s" to be a string value. Got "int".',
                Config::EXTRA_CONFIG_KEY,
                Config::SOURCE_LINK,
            ),
        ];

        yield 'empty string source link' => [
            [
                Config::EXTRA_CONFIG_KEY => [
                    Config::SOURCE_LINK => '',
                ],
            ],
            sprintf(
                'Expected setting "extra.%s.%s" to be a non-empty-string value.',
                Config::EXTRA_CONFIG_KEY,
                Config::SOURCE_LINK,
            ),
        ];

        yield 'non string target directory' => [
            [
                Config::EXTRA_CONFIG_KEY => [
					Config::SOURCE_LINK => 'https://github.com/neontsun/composer-devtools-plugin',
                    Config::TARGET_DIRECTORY => 123,
                ],
            ],
            sprintf(
                'Expected setting "extra.%s.%s" to be a string value. Got "int".',
                Config::EXTRA_CONFIG_KEY,
                Config::TARGET_DIRECTORY,
            ),
        ];

        yield 'empty string target directory' => [
            [
                Config::EXTRA_CONFIG_KEY => [
					Config::SOURCE_LINK => 'https://github.com/neontsun/composer-devtools-plugin',
                    Config::TARGET_DIRECTORY => '',
                ],
            ],
            sprintf(
                'Expected setting "extra.%s.%s" to be a non-empty-string value.',
                Config::EXTRA_CONFIG_KEY,
                Config::TARGET_DIRECTORY,
            ),
        ];

        yield 'non bool update gitignore' => [
            [
                Config::EXTRA_CONFIG_KEY => [
					Config::SOURCE_LINK => 'https://github.com/neontsun/composer-devtools-plugin',
                    Config::UPDATE_GITIGNORE => '',
                ],
            ],
            sprintf(
                'Expected setting "extra.%s.%s" to be a bool value. Got "string".',
                Config::EXTRA_CONFIG_KEY,
                Config::UPDATE_GITIGNORE,
            ),
        ];
    }
}
