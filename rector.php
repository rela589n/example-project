<?php

declare(strict_types=1);

use PhPhD\ExceptionalValidation\Upgrade\ExceptionalValidationSetList;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/public',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withImportNames(removeUnusedImports: true)
    ->withSets(ExceptionalValidationSetList::fromVersion('1.4')->getSetList())
    ->withSkip([__DIR__.'/src/Kernel.php', __DIR__.'/config/bundles.php'])
    // uncomment to reach your current PHP version
    // ->withPhpSets()
    ->withTypeCoverageLevel(0)
    ->withDeadCodeLevel(0)
    ->withCodeQualityLevel(0);
