<?php

declare(strict_types = 1);

namespace Neontsun\Composer\Devtools\Exception;

use Exception;

use function sprintf;

final class UnexpectedTypeException extends Exception
{
    public function __construct(string $expected, mixed $actual)
    {
        parent::__construct(sprintf('Expected argument of type "%s", "%s" given', $expected, get_debug_type($actual)));
    }
}
