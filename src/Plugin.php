<?php

declare(strict_types = 1);

namespace Neontsun\Composer\Devtools;

use Composer\Composer;
use Composer\Console\Application;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\ConsoleIO;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider as ComposerCommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Neontsun\Composer\Devtools\Command\DownloadCommand;
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
            ScriptEvents::POST_INSTALL_CMD => 'onPostInstall',
            ScriptEvents::POST_UPDATE_CMD => 'onPostUpdate',
        ];
    }

    /**
     * @throws ExceptionInterface
     */
    public function onPostInstall(Event $event): void
    {
        $this->logger->debug(sprintf('Start handling <info>%s</info> event', ScriptEvents::POST_INSTALL_CMD));

        $this->proxyToEventHandlerWithPublicIOResolve($event);
    }

    /**
     * @throws ExceptionInterface
     */
    public function onPostUpdate(Event $event): void
    {
        $this->logger->debug(sprintf('Start handling <info>%s</info> event', ScriptEvents::POST_UPDATE_CMD));

        $this->proxyToEventHandlerWithPublicIOResolve($event);
    }

    /**
     * @throws ExceptionInterface
     */
    private function proxyToEventHandlerWithPublicIOResolve(Event $event): void
    {
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

        $this->onEvent($publicIO->getInput(), $publicIO->getOutput());
    }

    /**
     * @throws ExceptionInterface
     */
    private function onEvent(InputInterface $input, OutputInterface $output): void
    {
        $this->logger->debug('Starting event processing');

        $io = $this->io;

        $application = new Application();

        $downloadCommand = new DownloadCommand();
        $downloadCommand->setComposer($this->composer);
        $downloadCommand->setApplication($application);
        $downloadCommand->setIO($io);

        $downloadCommand->run($input, $output);
    }
}
