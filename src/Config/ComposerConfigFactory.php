<?php

declare(strict_types = 1);

namespace Neontsun\Composer\Devtools\Config;

use Composer\Config as ComposerConfig;
use Composer\Factory;
use Composer\Json\JsonFile;
use Composer\Json\JsonValidationException;
use InvalidArgumentException;
use RuntimeException;
use Seld\JsonLint\ParsingException;

final readonly class ComposerConfigFactory
{
    private function __construct() {}

    /**
     * @throws JsonValidationException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws ParsingException
     */
    public static function create(): ComposerConfig
    {
        $config = Factory::createConfig();

        $jsonFile = new JsonFile(Factory::getComposerFile());

        if (! $jsonFile->exists()) {
            return $config;
        }

        $jsonFile->validateSchema(JsonFile::LAX_SCHEMA);

        return $config;
    }
}
