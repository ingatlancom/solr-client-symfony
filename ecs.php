<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Basic\PsrAutoloadingFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoUnreachableDefaultArgumentValueFixer;
use PhpCsFixer\Fixer\FunctionNotation\StaticLambdaFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocToCommentFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitSetUpTearDownVisibilityFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer;
use PhpCsFixer\Fixer\ReturnNotation\NoUselessReturnFixer;
use PhpCsFixer\Fixer\StringNotation\HeredocToNowdocFixer;
use PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set('sets', ['php70', 'php71', 'psr12', 'clean-code', 'symfony', 'symfony-risky']);
    $parameters->set('paths', [__DIR__ . '/src', __DIR__ . '/tests']);
    $parameters->set('skip', [PhpdocToCommentFixer::class => null]);

    $services = $containerConfigurator->services();
    $services->set(HeaderCommentFixer::class)
    ->call('configure', [['header' => <<<'HEADER'
        This file is part of Solr Client Symfony package.

        (c) ingatlan.com Zrt. <fejlesztes@ingatlan.com>

        This source file is subject to the MIT license that is bundled
        with this source code in the file LICENSE.
        HEADER
    ]]);
    $services->set(PsrAutoloadingFixer::class);
    $services->set(OrderedClassElementsFixer::class);
    $services->set(ProtectedToPrivateFixer::class);
    $services->set(NoUselessElseFixer::class);
    $services->set(NativeFunctionInvocationFixer::class)
        ->call('configure', [['include' => ['@compiler_optimized']]]);
    $services->set(NoUnreachableDefaultArgumentValueFixer::class);
    $services->set(StaticLambdaFixer::class);
    $services->set(PhpdocAlignFixer::class)
        ->call('configure', [['align' => 'left']]);
    $services->set(PhpUnitMethodCasingFixer::class)
        ->call('configure', [['case' => 'snake_case']]);
    $services->set(PhpUnitSetUpTearDownVisibilityFixer::class);
    $services->set(PhpUnitTestAnnotationFixer::class)
        ->call('configure', [['style' => 'annotation']]);
    $services->set(NoUselessReturnFixer::class);
    $services->set(HeredocToNowdocFixer::class);
    $services->set(ArrayIndentationFixer::class);
};
