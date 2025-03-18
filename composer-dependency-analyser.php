<?php

declare(strict_types = 1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return (new Configuration())
	->ignoreErrorsOnPackage('composer/composer', [ErrorType::DEV_DEPENDENCY_IN_PROD]);