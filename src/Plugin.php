<?php

declare(strict_types = 1);

namespace Neontsun\ComposerDevtoolsPlugin;

use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Plugin\PluginInterface;

final readonly class Plugin implements EventSubscriberInterface, PluginInterface {}
