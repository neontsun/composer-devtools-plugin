<?php

declare(strict_types = 1);

namespace Neontsun\Composer\Devtools\Command;

use Composer\Command\BaseCommand;
use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use Composer\Util\Filesystem;
use Composer\Util\Silencer;
use Exception;
use Neontsun\Composer\Devtools\Config\Config;
use Neontsun\Composer\Devtools\Logger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

final class UpdateGitIgnoreCommand extends BaseCommand
{
    public function __construct(
        private Logger $logger = new Logger(new NullIO()),
    ) {
        parent::__construct('devtools:update-gitignore');
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
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->debug(sprintf('Starting execute <info>%s</info>', self::class));

		$currentWorkingDir = getcwd();
		$gitignorePath = implode(DIRECTORY_SEPARATOR, [$currentWorkingDir, '.gitignore']);
		
		$config = Config::fromComposer($this->requireComposer());
		
		if (! file_exists($gitignorePath)) {
			$this->logger->debug(sprintf('File <comment>%s</comment> does not exists. Try to make file.', $gitignorePath));
			
			$success = @file_put_contents($gitignorePath, $config->getTargetDirectory());
			
			if (false === $success) {
				$this->logger->debug(sprintf('<warning>Create file %s with target directory ended is failure</warning>', $gitignorePath));
				
				return self::FAILURE;
			}
			
			$this->logger->debug(sprintf('Completion of <info>%s</info> execution', self::class));
			
			return self::SUCCESS;
		}
		
		if (! Filesystem::isReadable($gitignorePath)) {
			$this->logger->debug(sprintf('<warning>File %s is not readable</warning>', $gitignorePath));
			
			return self::FAILURE;
		}
		
		$currentContent = Silencer::call('file_get_contents', $gitignorePath);
		
		$this->logger->debug(sprintf('Check target directory exist in <comment>%s</comment> file and append if dont', $gitignorePath));
		
		$currentContentArray = explode(PHP_EOL, $currentContent);
		
		if (! in_array($config->getTargetDirectory(), $currentContentArray, true)) {
			$currentContentArray[] = $config->getTargetDirectory();
		}
		
		$currentContentArray = array_filter($currentContentArray, static function (string $value): bool {
			return '' !== $value;
		});
		
		$currentContent = implode(PHP_EOL, $currentContentArray);
		
		$this->logger->debug(sprintf('Put content to <comment>%s</comment> file', $gitignorePath));
		
		$success = (new Filesystem())->filePutContentsIfModified($gitignorePath, $currentContent);

		if (! $success) {
			$this->logger->debug(sprintf('<warning>Put to file %s with target directory ended is failure</warning>', $gitignorePath));
			
			return self::FAILURE;
		}
		
        $this->logger->debug(sprintf('Completion of <info>%s</info> execution', self::class));

        return self::SUCCESS;
    }
}
