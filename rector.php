<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__.'/src',
    ]);
    $rectorConfig->skip([
        __DIR__.'/src/Entity/',
        __DIR__.'/src/Service/ApiEntityMaker.php',
    ]);

    // register a single rule
//    $rectorConfig->rule(ClassPropertyAssignToConstructorPromotionRector::class);

    // define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_81,
    ]);
};
