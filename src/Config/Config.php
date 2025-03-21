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

    public const SOURCE_LINK = 'source-link';
	public const TARGET_DIRECTORY = 'target-directory';
	public const UPDATE_GITIGNORE = 'update-gitignore';
	
	public const TARGET_DIRECTORY_DEFAULT = 'tools';
	public const UPDATE_GITIGNORE_DEFAULT = true; 
	
    private const DEFAULT_CONFIG = [
        self::SOURCE_LINK => null,
		self::TARGET_DIRECTORY => self::TARGET_DIRECTORY_DEFAULT,
		self::UPDATE_GITIGNORE => self::UPDATE_GITIGNORE_DEFAULT,
    ];
	
	/**
	 * @var non-empty-string
	 */
    private string $sourceLink;
	/**
	 * @var non-empty-string
	 */
	private string $targetDirectory;
	private bool $updateGitignore;
	
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

        $sourceLink = $devtoolsExtra[self::SOURCE_LINK];

        if (! is_string($sourceLink)) {
            throw new InvalidComposerExtraConfigException(
                sprintf(
                    'Expected setting "extra.%s.%s" to be a string value. Got "%s".',
                    self::EXTRA_CONFIG_KEY,
                    self::SOURCE_LINK,
                    get_debug_type($sourceLink),
                ),
            );
        }
		
		if ('' === $sourceLink) {
			throw new InvalidComposerExtraConfigException(
				sprintf(
					'Expected setting "extra.%s.%s" to be a non-empty-string value.',
					self::EXTRA_CONFIG_KEY,
					self::SOURCE_LINK,
				),
			);
		}

		$targetDirectory = $devtoolsExtra[self::TARGET_DIRECTORY];
		
		if (! is_string($targetDirectory)) {
			throw new InvalidComposerExtraConfigException(
				sprintf(
					'Expected setting "extra.%s.%s" to be a string value. Got "%s".',
					self::EXTRA_CONFIG_KEY,
					self::TARGET_DIRECTORY,
					get_debug_type($targetDirectory),
				),
			);
		}
		
		if ('' === $targetDirectory) {
			throw new InvalidComposerExtraConfigException(
				sprintf(
					'Expected setting "extra.%s.%s" to be a non-empty-string value.',
					self::EXTRA_CONFIG_KEY,
					self::TARGET_DIRECTORY,
				),
			);
		}
		
		$updateGitignore = $devtoolsExtra[self::UPDATE_GITIGNORE];
		
		if (! is_bool($updateGitignore)) {
			throw new InvalidComposerExtraConfigException(
				sprintf(
					'Expected setting "extra.%s.%s" to be a bool value. Got "%s".',
					self::EXTRA_CONFIG_KEY,
					self::UPDATE_GITIGNORE,
					get_debug_type($updateGitignore),
				),
			);
		}
		
        $this->sourceLink = $sourceLink;
		$this->targetDirectory = $targetDirectory;
		$this->updateGitignore = $updateGitignore;
    }

    /**
     * @throws InvalidComposerExtraConfigException
     */
    public static function fromComposer(Composer $composer): self
    {
        return new self($composer->getPackage()->getExtra());
    }
	
	/**
	 * @return non-empty-string
	 */
    public function getSourceLink(): string
    {
        return $this->sourceLink;
    }
	
	/**
	 * @return non-empty-string
	 */
	public function getTargetDirectory(): string
	{
		return $this->targetDirectory;
	}
	
	public function needUpdateGitIgnore(): bool
	{
		return $this->updateGitignore;
	}
}
