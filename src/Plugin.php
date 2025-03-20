<?php

declare(strict_types = 1);

namespace Neontsun\Composer\Devtools;

use Composer\Composer;
use Composer\Console\Application;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\ConsoleIO;
use Composer\IO\IOInterface;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Neontsun\Composer\Devtools\Command\DownloadCommand;
use Neontsun\Composer\Devtools\Config\Config;
use Neontsun\Composer\Devtools\Exception\InvalidComposerExtraConfigException;
use Neontsun\Composer\Devtools\Provider\CommandProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Plugin\Capability\CommandProvider as ComposerCommandProvider;

final readonly class Plugin implements EventSubscriberInterface, PluginInterface, Capable
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
			ScriptEvents::POST_INSTALL_CMD => 'onPostInstall',
			ScriptEvents::POST_UPDATE_CMD => 'onPostUpdate',
        ];
    }
	
	/**
	 * @throws InvalidComposerExtraConfigException
	 */
	public function onPostInstall(Event $event): void
	{
		$this->logger->debug('Calling onPostInstall().');
		
		$this->proxyWithResolveIOInstance($event);
	}
	
	/**
	 * @throws InvalidComposerExtraConfigException
	 */
	public function onPostUpdate(Event $event): void
	{
		$this->logger->debug('Calling onPostUpdate().');
		
		$this->proxyWithResolveIOInstance($event);
	}
	
	/**
	 * @throws InvalidComposerExtraConfigException
	 */
	private function proxyWithResolveIOInstance(Event $event): void
	{
		$io = $event->getIO();
		
		if (! $io instanceof ConsoleIO) {
			$this->logger->debug('Canceling event processing because Event did not return a ConsoleIO instance.');
			
			return;
		}
		
		$publicIO = PublicIO::fromConsoleIO($io);
		
		$this->onEvent($publicIO->getInput(), $publicIO->getOutput());
	}
	
	/**
	 * @throws InvalidComposerExtraConfigException
	 */
	private function onEvent(InputInterface $input, OutputInterface $output): void
	{
		$io = $this->io;
		
		$application = new Application();
		
		$downloadCommand = new DownloadCommand();
		$downloadCommand->setComposer($this->composer);
		$downloadCommand->setApplication($application);
		$downloadCommand->setIO($io);
		
		$downloadCommand->run($input, $output);
	}
}
