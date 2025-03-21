<?php

declare(strict_types = 1);

namespace Neontsun\Composer\Devtools\Provider;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Neontsun\Composer\Devtools\Command\DownloadCommand;
use Neontsun\Composer\Devtools\Command\InstallCommand;
use Neontsun\Composer\Devtools\Command\UpdateGitIgnoreCommand;
use Symfony\Component\Console\Exception\LogicException;

final readonly class CommandProvider implements CommandProviderCapability
{
    /**
     * @throws LogicException
     */
    public function getCommands(): array
    {
        return [
            new DownloadCommand(),
            new UpdateGitIgnoreCommand(),
            new InstallCommand(),
        ];
    }
}
