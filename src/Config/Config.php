<?php

declare(strict_types = 1);

namespace Neontsun\Composer\Devtools\Config;

use Composer\Composer;
use Neontsun\Composer\Devtools\Exception\InvalidComposerExtraConfigException;

use function is_array;
use function is_string;
use function sprintf;

final readonly class Config
{
    public const EXTRA_CONFIG_KEY = 'devtools';

    public const REPOSITORY = 'repository';

    private const DEFAULT_CONFIG = [
        self::REPOSITORY => '',
    ];

    private string $repository;

    /**
     * @param array<array-key, mixed> $extra
     * @throws InvalidComposerExtraConfigException
     */
    public function __construct(array $extra)
    {
        $devtoolsExtra = $extra[self::EXTRA_CONFIG_KEY] ?? [];

        if (! is_array($devtoolsExtra)) {
            throw new InvalidComposerExtraConfigException(
                sprintf(
                    'Expected setting "extra.%s" to be a array value. Got "%s".',
                    self::EXTRA_CONFIG_KEY,
                    get_debug_type($devtoolsExtra),
                ),
            );
        }

        $devtoolsExtra = array_merge(self::DEFAULT_CONFIG, $devtoolsExtra);

        $repository = $devtoolsExtra[self::REPOSITORY];

        if (! is_string($repository)) {
            throw new InvalidComposerExtraConfigException(
                sprintf(
                    'Expected setting "extra.%s.%s" to be a string value. Got "%s".',
                    self::EXTRA_CONFIG_KEY,
                    self::REPOSITORY,
                    get_debug_type($repository),
                ),
            );
        }
		
        $this->repository = $repository;
    }

    /**
     * @throws InvalidComposerExtraConfigException
     */
    public static function fromComposer(Composer $composer): self
    {
        return new self($composer->getPackage()->getExtra());
    }
	
    public function getRepository(): string
    {
        return $this->repository;
    }
	
	public function repositoryIsEmpty(): bool
	{
		return '' === $this->repository;
	}
}
