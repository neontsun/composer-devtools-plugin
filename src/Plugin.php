<?php

declare(strict_types = 1);

namespace Neontsun\Composer\Devtools;

use Composer\Command\BaseCommand;
use Composer\Composer;
use Composer\Console\Application;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\ConsoleIO;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider as ComposerCommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\CommandEvent;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Neontsun\Composer\Devtools\Command\DownloadCommand;
use Neontsun\Composer\Devtools\Command\InstallCommand;
use Neontsun\Composer\Devtools\Command\UpdateGitIgnoreCommand;
use Neontsun\Composer\Devtools\Config\Config;
use Neontsun\Composer\Devtools\Exception\InvalidComposerExtraConfigException;
use Neontsun\Composer\Devtools\Provider\CommandProvider;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

final class Plugin implements Capable, EventSubscriberInterface, PluginInterface
{
    private IOInterface $io;

    private Composer $composer;

    private Logger $logger;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->io = $io;
        $this->composer = $composer;
        $this->logger = new Logger($io);
    }

    public function deactivate(Composer $composer, IOInterface $io): void {}

    public function uninstall(Composer $composer, IOInterface $io): void {}

    public function getCapabilities(): array
    {
        return [
            ComposerCommandProvider::class => CommandProvider::class,
        ];
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PluginEvents::COMMAND => 'onCommand',
            ScriptEvents::POST_AUTOLOAD_DUMP => 'onPostAutoloadDump',
        ];
    }

    /**
     * @throws ExceptionInterface
     * @throws InvalidComposerExtraConfigException
     */
    public function onCommand(CommandEvent $event): void
    {
        $this->logger->debug(sprintf('Start handling <info>%s</info> event', PluginEvents::COMMAND));

        $this->onEvent(
            $event->getCommandName(),
            $event->getInput(),
            $event->getOutput(),
        );
    }

    /**
     * @throws ExceptionInterface
     * @throws InvalidComposerExtraConfigException
     */
    public function onPostAutoloadDump(Event $event): void
    {
        $this->logger->debug(sprintf('Start handling <info>%s</info> event', ScriptEvents::POST_AUTOLOAD_DUMP));

        $io = $event->getIO();

        if (! $io instanceof ConsoleIO) {
            $this->logger->debug(
                sprintf(
                    '<warning>Canceling event processing because %s did not return a ConsoleIO instance</warning>',
                    Event::class,
                ),
            );

            return;
        }

        $publicIO = PublicIO::fromConsoleIO($io);

        $this->logger->debug('Public I/O resolved');

		$eventInput = $publicIO->getInput();
		$commandName = $eventInput->getArgument('command');
		
		if (! is_string($commandName) || '' === $commandName) {
			$this->logger->debug('<warning>Canceling event processing because event input not contain command name</warning>');
			
			return;
		}
		
        $this->onEvent(
			$commandName,
			$eventInput,
            $publicIO->getOutput(),
        );
    }

    /**
     * @throws ExceptionInterface
     * @throws InvalidComposerExtraConfigException
     */
    private function onEvent(string $commandName, InputInterface $input, OutputInterface $output): void
    {
        $this->logger->debug('Starting event processing');

        if ('install' !== $commandName) {
            $this->logger->debug('Unsupported command. Canceling event processing');

            return;
        }

        $this->logger->debug(
            sprintf(
                'Original input: <comment>%s</comment>.',
                $input->__toString(),
            ),
        );

        $io = $this->io;

        $application = new Application();
        $config = Config::fromComposer($this->composer);

        $updateGitIgnoreCommand = null;

        if ($config->needUpdateGitIgnore()) {
            $updateGitIgnoreCommand = new UpdateGitIgnoreCommand();
        }

        $downloadCommand = new DownloadCommand();
        $installCommand = new InstallCommand();

        $this->fillCommand(
            $application,
            $io,
            $downloadCommand,
            $updateGitIgnoreCommand,
            $installCommand,
        );

        $downloadCommand->run($input, $output);
        $updateGitIgnoreCommand?->run($input, $output);
        $installCommand->run($input, $output);
    }

    private function fillCommand(Application $application, IOInterface $io, ?BaseCommand ...$commands): void
    {
        foreach ($commands as $command) {
            if (null === $command) {
                continue;
            }

            $command->setComposer($this->composer);
            $command->setApplication($application);
            $command->setIO($io);
        }
    }
}
