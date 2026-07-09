<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/src',
        __DIR__.'/routes',
        __DIR__.'/tests',
        __DIR__.'/resources',
    ])
    ->withSets([

    ])
    ->withTypeCoverageLevel(8)
    ->withCodeQualityLevel(6)
    ->withPreparedSets(
        deadCode: true,
        earlyReturn: true,
    );
