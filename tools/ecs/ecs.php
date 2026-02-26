<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\PhpUnit\PhpUnitStrictFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->import(__DIR__ . '/vendor/brick/coding-standard/ecs.php');

    $libRootPath = realpath(__DIR__ . '/../..');

    $ecsConfig->paths(
        [
            $libRootPath . '/src',
            $libRootPath . '/tests',
            __FILE__,
        ],
    );

    $ecsConfig->skip([
        // We do need assertEquals() in JsonDecoderTest
        PhpUnitStrictFixer::class => $libRootPath . '/tests/AbstractTestCase.php',
    ]);
};
