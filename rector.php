<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\MethodCall\RemoveNullArgOnNullDefaultParamRector;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPhpSets(php84: true)
    ->withSets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
    ])
    // Shorten FQCNs in docblocks too, matching pint.json's default fully_qualified_strict_types.
    ->withImportNames(importDocBlockNames: true, removeUnusedImports: true)
    ->withSkip([
        // Tests pass explicit `null` to assert it matches the omitted-argument default;
        // collapsing the call would delete the behaviour under test.
        RemoveNullArgOnNullDefaultParamRector::class => [__DIR__.'/tests'],
    ]);
