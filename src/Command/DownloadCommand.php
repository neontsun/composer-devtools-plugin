<?php

declare(strict_types = 1);

namespace Neontsun\Composer\Devtools\Command;

use Composer\Command\BaseCommand;
use Composer\Downloader\GitDownloader;
use Composer\Downloader\SvnDownloader;
use Composer\Downloader\ZipDownloader;
use Composer\Factory;
use Composer\Installer;
use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use Composer\Package\Package;
use Composer\Repository\RepositoryManager;
use Composer\Repository\VcsRepository;
use Composer\Util\Filesystem;
use Neontsun\Composer\Devtools\Config\ComposerConfigFactory;
use Neontsun\Composer\Devtools\Config\Config;
use Neontsun\Composer\Devtools\Logger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

final class DownloadCommand extends BaseCommand
{
	private Logger $logger;
	
	public function __construct(
		?Logger $logger = null,
	) {
		parent::__construct('devtools:download');
		
		$this->logger = $logger ?? new Logger(new NullIO());
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
	
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$composer = $this->requireComposer();
		
		$config = Config::fromComposer($composer);
		$composerConfig = $composer->getConfig();
		
		if ($config->repositoryIsEmpty()) {
			$this->logger->info('The repository option in the configuration is not filled in. The event processing has been terminated.');
			
			return self::SUCCESS;
		}
		
		$this->logger->info('The repository option in the configuration is filled. Event processing continues.');
		
		$currentWorkingDir = getcwd();
		$destinationDir = implode(DIRECTORY_SEPARATOR, [$currentWorkingDir, 'tools']);
		
		$filesystem = new Filesystem();
		
		$filesystem->removeDirectory($destinationDir);
		$filesystem->ensureDirectoryExists($destinationDir);
		
		$io = $this->getIO();
		
		$package = new Package('dummy-package', '1.0.0', '1.0.0');
		$package->setDistType('zip');
		$package->setDistUrl($config->getRepository());
		
		$downloader = new ZipDownloader($io, $composerConfig, $composer->getLoop()->getHttpDownloader());
		
		$this->logger->debug(sprintf('Using %s downloader', $downloader::class));
		
		$downloader->download($package, $destinationDir);
		$zip = new ZipArchive();
		
		return self::SUCCESS;
	}
}