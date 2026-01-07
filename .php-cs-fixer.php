<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/aksara',
        __DIR__ . '/install',
        __DIR__ . '/modules',
        __DIR__ . '/tests'
    ])
    ->notPath([
        'Views',
        'views'
    ]);

$config = new PhpCsFixer\Config();
$header = <<<EOF
    This file is part of Aksara CMS, both framework and publishing
    platform.

    @author     Aby Dahana <abydahana@gmail.com>
    @copyright  (c) Aksara Laboratory <https://aksaracms.com>
    @license    MIT License

    This source file is subject to the MIT license that is bundled
    with this source code in the LICENSE.txt file.

    When the signs is coming, those who don't believe at "that time"
    have only two choices, commit suicide or become brutal.
    EOF;
return $config->setRules
([
    '@PSR12' => true,
    'header_comment' => [
        'header' => $header,
        'location' => 'after_open',
        'comment_type' => 'PHPDoc'
    ],
    'array_syntax' => ['syntax' => 'short'],
    'assign_null_coalescing_to_coalesce_equal' => false,
    'braces' => [
        'allow_single_line_anonymous_class_with_empty_body' => true,
        'allow_single_line_closure' => true,
        'position_after_anonymous_constructs' => 'next',
        'position_after_control_structures' => 'next',
        'position_after_functions_and_oop_constructs' => 'next',
    ],
    'binary_operator_spaces' => [
        'default' => 'single_space'
    ],
    'whitespace_after_comma_in_array' => [
        'ensure_single_space' => true
    ],
    'not_operator_with_successor_space' => true,
    'method_chaining_indentation' => false,
    'phpdoc_indent' => true,
    'phpdoc_trim' => true,
    'no_blank_lines_after_phpdoc' => true,
    'no_superfluous_phpdoc_tags' => [
        'remove_inheritdoc' => true
    ],
    'phpdoc_add_missing_param_annotation' => [
        'only_untyped' => false
    ],
    'yoda_style' => [
        'always_move_variable' => true
    ]
])
->setFinder($finder)
->setIndent("    ")
->setLineEnding("\n");
