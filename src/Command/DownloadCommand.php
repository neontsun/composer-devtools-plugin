<?php

declare(strict_types = 1);

namespace Neontsun\Composer\Devtools\Command;

use const DIRECTORY_SEPARATOR;

use Composer\Command\BaseCommand;
use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use Composer\Util\SyncHelper;
use Exception;
use InvalidArgumentException;
use Neontsun\Composer\Devtools\Config\Config;
use Neontsun\Composer\Devtools\Exception\InvalidComposerExtraConfigException;
use Neontsun\Composer\Devtools\Logger;
use Neontsun\Composer\Devtools\Package\PackageFactory;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

final class DownloadCommand extends BaseCommand
{
    public function __construct(
        private Logger $logger = new Logger(new NullIO()),
    ) {
        parent::__construct('devtools:download');
    }

    public function isProxyCommand(): bool
    {
        return false;
    }

    public function setIO(IOInterface $io): void
    {
        parent::setIO($io);

        $this->logger = new Logger($io);
    }

    public function getIO(): IOInterface
    {
        $io = parent::getIO();

        $this->logger = new Logger($io);

        return $io;
    }

    /**
     * @throws InvalidArgumentException
     * @throws InvalidComposerExtraConfigException
     * @throws RuntimeException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->debug(sprintf('Starting execute <info>%s</info>', self::class));

        $composer = $this->requireComposer();

        $config = Config::fromComposer($composer);

        if ($config->repositoryIsEmpty()) {
            $this->logger->info('The repository option in the configuration is not filled in. The event processing has been terminated.');

            return self::SUCCESS;
        }

        $this->logger->info('The repository option in the configuration is filled. Event processing continues.');

        $currentWorkingDir = getcwd();
        $destinationDir = implode(DIRECTORY_SEPARATOR, [$currentWorkingDir, 'tools']);

        $this->logger->debug(sprintf('Current working directory: <comment>%s</comment>', $currentWorkingDir));

        $package = PackageFactory::create($config);

        $this->logger->debug('Success create temporary package');

        $dm = $composer->getDownloadManager();
        $downloader = $dm->getDownloader($package->getDistType());

        $this->logger->debug(sprintf('Using <comment>%s</comment>', $downloader::class));
        $this->logger->debug('Starting sync download and install temporary package');

        SyncHelper::downloadAndInstallPackageSync(
            $composer->getLoop(),
            $downloader,
            $destinationDir,
            $package,
        );

        $this->logger->debug(sprintf('completion of <info>%s</info> execution', self::class));

        return self::SUCCESS;
    }
}
