<?php

declare(strict_types = 1);

namespace Neontsun\Composer\Devtools\Provider;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Neontsun\Composer\Devtools\Command\DownloadCommand;

final readonly class CommandProvider implements CommandProviderCapability
{
	public function getCommands(): array
	{
		return [
			new DownloadCommand(),
		];
	}
}