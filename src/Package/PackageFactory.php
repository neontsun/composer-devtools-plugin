<?php

declare(strict_types = 1);

namespace Neontsun\Composer\Devtools\Package;

use Neontsun\Composer\Devtools\Config\Config;
use Random\RandomException;

final class PackageFactory
{
    /**
     * @throws RandomException
     */
    public static function create(Config $config): TemporaryPackage
    {
        $temporaryPackage = new TemporaryPackage();
        $temporaryPackage->setDistType('zip');
        $temporaryPackage->setDistUrl($config->getRepository());

        return $temporaryPackage;
    }
}
