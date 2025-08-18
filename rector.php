<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withParallel()
    ->withRootFiles()
    ->withPaths([__DIR__.'/src', __DIR__.'/tests'])
    ->withPhpSets(php84: true)
    ->withSkip([])
;
