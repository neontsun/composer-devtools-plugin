<?php

declare(strict_types = 1);

namespace Neontsun\Composer\Devtools\Command;

use const DIRECTORY_SEPARATOR;
use const GLOB_ONLYDIR;

use Composer\Command\BaseCommand;
use Composer\Console\Application;
use Composer\Downloader\TransportException;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use Neontsun\Composer\Devtools\Config\Config;
use Neontsun\Composer\Devtools\Exception\InvalidComposerExtraConfigException;
use Neontsun\Composer\Devtools\Logger;
use RuntimeException;
use Seld\JsonLint\ParsingException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use function sprintf;

final class InstallCommand extends BaseCommand
{
    public function __construct(
        private Logger $logger = new Logger(new NullIO()),
    ) {
        parent::__construct('devtools:install');
    }

    public function isProxyCommand(): bool
    {
        return true;
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

    protected function configure(): void
    {
        $this
            ->setDescription('Install dependencies in devtools namespaces')
            ->ignoreValidationErrors();
    }

    /**
     * @throws ParsingException
     * @throws RuntimeException
     * @throws Throwable
     * @throws TransportException
     * @throws InvalidComposerExtraConfigException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->debug(sprintf('Starting execute <info>%s</info>', self::class));

        $config = Config::fromComposer($this->requireComposer());

        $currentWorkingDirectory = getcwd();

        if (false === $currentWorkingDirectory) {
            throw new RuntimeException('Unable to determine working directory');
        }

        $this->logger->debug(sprintf('Current working directory: <comment>%s</comment>', $currentWorkingDirectory));

        $targetDirectory = $currentWorkingDirectory . DIRECTORY_SEPARATOR . $config->getTargetDirectory();

        $packages = $this->getPackagesDirectories($targetDirectory);

        if ([] === $packages) {
            $this->logger->info('<warning>Could not packages for install</warning>');

            return self::SUCCESS;
        }

        $this->resetComposers();

        $exitCode = 0;

        foreach ($packages as $package) {
            $exitCode += $this->install($currentWorkingDirectory, $package, $output);
        }

        $this->logger->debug(sprintf('Completion of <info>%s</info> execution', self::class));

        return min($exitCode, 255);
    }

    /**
     * @param non-empty-string $directory
     * @return list<string>
     * @throws RuntimeException
     */
    private function getPackagesDirectories(string $directory): array
    {
        $directories = glob($directory . '/*', GLOB_ONLYDIR);

        if (false === $directories) {
            throw new RuntimeException(sprintf('Could not glob packages in directory %s', $directory));
        }

        return $directories;
    }

    /**
     * @param non-empty-string $workingDirectory
     * @throws RuntimeException
     * @throws Throwable
     * @throws TransportException
     * @throws ParsingException
     */
    private function install(
        string $workingDirectory,
        string $packageDirectoryName,
        OutputInterface $output,
    ): int {
        $this->logger->info(sprintf('Checking directory <comment>%s</comment>', $packageDirectoryName));

        $application = new Application();

        $cleanUp = function() use ($workingDirectory): void {
            $this->chdir($workingDirectory);
            $this->resetComposers();
        };

        $this->chdir($packageDirectoryName);

        if (! file_exists(Factory::getComposerFile())) {
            $this->logger->info(sprintf('<warning>Composer file not found in %s</warning>', $packageDirectoryName));

            return self::FAILURE;
        }

        $stringInput = new StringInput('install --working-dir=.');

        $this->logger->debug(sprintf('Running <info>`@composer %s`</info>', $stringInput->__toString()));

        try {
            $exitCode = $application->doRun($stringInput, $output);
        } catch (Throwable $e) {
            $cleanUp();

            throw $e;
        }

        $cleanUp();

        return $exitCode;
    }

    /**
     * @throws RuntimeException
     */
    private function resetComposers(): void
    {
        $application = $this->getApplication();

        $application->resetComposer();

        foreach ($application->all() as $command) {
            if (! $command instanceof BaseCommand) {
                continue;
            }

            $command->resetComposer();
        }
    }

    private function chdir(string $directory): void
    {
        chdir($directory);

        $this->logger->debug(sprintf('Changed current directory to <comment>%s</comment>.', $directory));
    }
}
