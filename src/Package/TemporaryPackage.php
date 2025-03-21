<?php

declare(strict_types = 1);

namespace Neontsun\Composer\Devtools\Package;

use Composer\Package\Package;
use Neontsun\Composer\Devtools\Exception\UnexpectedTypeException;
use Random\RandomException;

final class TemporaryPackage extends Package
{
    /**
     * @inheritDoc
     * @throws RandomException
     */
    public function __construct()
    {
        parent::__construct(bin2hex(random_bytes(6)), '1.0.0', '1.0.0');
    }

    /**
     * @inheritDoc
     * @throws UnexpectedTypeException
     */
    public function getDistType(): string
    {
        $type = parent::getDistType();

        if (null === $type) {
            throw new UnexpectedTypeException('string', $type);
        }

        return $type;
    }
}
