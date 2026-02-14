<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/public',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPreparedSets(
        psr12: true,
        common: true,
        strict: true,
        cleanCode: true,
    )
    ->withRules([
        PhpCsFixer\Fixer\ClassNotation\SelfStaticAccessorFixer::class,
        PhpCsFixer\Fixer\ControlStructure\SimplifiedIfReturnFixer::class,
        PhpCsFixer\Fixer\Phpdoc\PhpdocTagCasingFixer::class,
        PhpCsFixer\Fixer\FunctionNotation\NullableTypeDeclarationForDefaultNullValueFixer::class,
        PhpCsFixer\Fixer\Whitespace\BlankLineBetweenImportGroupsFixer::class,
        PhpCsFixer\Fixer\PhpUnit\PhpUnitStrictFixer::class,
        PhpCsFixer\Fixer\Whitespace\CompactNullableTypeDeclarationFixer::class,
        PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer::class,
        PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer::class,
        PhpCsFixer\Fixer\Strict\StrictParamFixer::class,
        PhpCsFixer\Fixer\Strict\StrictComparisonFixer::class,
        PhpCsFixer\Fixer\Import\NoUnusedImportsFixer::class,
        Symplify\CodingStandard\Fixer\Spacing\StandaloneLinePromotedPropertyFixer::class,
        Symplify\CodingStandard\Fixer\Strict\BlankLineAfterStrictTypesFixer::class,
    ])
    ->withSkip([
        PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer::class, // using ModifierKeywordsFixer instead
        PhpCsFixer\Fixer\Phpdoc\PhpdocToCommentFixer::class,
        PhpCsFixer\Fixer\Basic\SingleLineEmptyBodyFixer::class,
        PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer::class,
        Symplify\CodingStandard\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer::class,
        __DIR__.'/src/Support/Contracts/gen',
        __DIR__.'/config/bundles.php',
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\ClassNotation\ModifierKeywordsFixer::class, [
        'elements' => [
            'const',
            'method',
        ],
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer::class, [
        'include' => ['@all'],
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer::class, [
        'import_classes' => true,
        'import_functions' => true,
        'import_constants' => false,
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\Import\OrderedImportsFixer::class, [
        'imports_order' => [
            'class',
            'function',
            'const',
        ],
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer::class, [
        'single_item_single_line' => true,
        'multi_line_extends_each_single_line' => true,
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer::class, [
        'elements' => [
            'property' => 'one',
            'method' => 'one',
        ],
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer::class, [
        'method' => 'single',
        'property' => 'single',
        'const' => 'single',
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\Phpdoc\PhpdocOrderByValueFixer::class, [
        'annotations' => [],
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer::class, [
        'align' => 'left',
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\Phpdoc\PhpdocTypesOrderFixer::class, [
        'sort_algorithm' => 'none',
        'null_adjustment' => 'always_last',
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\FunctionNotation\FopenFlagsFixer::class, [
        'b_mode' => true,
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\PhpUnit\PhpUnitTestCaseStaticMethodCallsFixer::class, [
        'call_type' => 'self',
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\CastNotation\CastSpacesFixer::class, [
        'space' => 'none',
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\Operator\ConcatSpaceFixer::class, [
        'spacing' => 'none',
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer::class, [
        'always_move_variable' => true,
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer::class, [
        'elements' => [
            'arrays',
            'arguments',
            'match',
            'parameters',
        ],
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer::class, [
        'strategy' => 'new_line_for_chained_calls',
    ])
    ->withConfiguredRule(PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer::class, [
        'statements' => [
            'break',
            'case',
            'continue',
            'declare',
            'default',
            'do',
            'for',
            'foreach',
            'if',
            'include',
            'include_once',
            'require',
            'require_once',
            'return',
            'switch',
            'throw',
            'try',
            'while',
            'yield',
            'yield_from',
        ],
    ]);
