<?php

declare(strict_types=1);

class NoTrailingWhitespaceInInlineHtmlFixer extends PhpCsFixer\AbstractFixer
{
    public function getName(): string
    {
        return 'Aksara/no_trailing_whitespace_in_inline_html';
    }

    public function getDefinition(): PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new PhpCsFixer\FixerDefinition\FixerDefinition(
            'Removes trailing whitespace on blank lines in inline HTML tokens.',
            []
        );
    }

    public function isCandidate(PhpCsFixer\Tokenizer\Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_INLINE_HTML);
    }

    protected function applyFix(\SplFileInfo $file, PhpCsFixer\Tokenizer\Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_INLINE_HTML)) {
                $content = $token->getContent();
                $fixed = preg_replace('/^[ \t]+(\r?\n)/m', '$1', $content);

                if ($fixed !== $content) {
                    if ($fixed === '') {
                        $tokens->clearAt($index);
                    } else {
                        $tokens[$index] = new PhpCsFixer\Tokenizer\Token([T_INLINE_HTML, $fixed]);
                    }
                }
            }
        }
    }
}

class ViewPhpTagFormattingFixer extends PhpCsFixer\AbstractFixer
{
    public function getName(): string
    {
        return 'Aksara/view_php_tag_formatting';
    }

    public function getDefinition(): PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new PhpCsFixer\FixerDefinition\FixerDefinition(
            'Formats PHP opening/closing tags and echo statements in view files.',
            []
        );
    }

    public function getPriority(): int
    {
        return -20;
    }

    public function isCandidate(PhpCsFixer\Tokenizer\Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_OPEN_TAG, T_CLOSE_TAG, T_ECHO]);
    }

    protected function applyFix(\SplFileInfo $file, PhpCsFixer\Tokenizer\Tokens $tokens): void
    {
        for ($index = count($tokens) - 1; $index >= 0; $index--) {
            if ($tokens[$index]->isGivenKind(T_CLOSE_TAG)) {
                $prevIndex = $tokens->getPrevNonWhitespace($index);

                if ($prevIndex !== null) {
                    $isEndingStatement = false;

                    if ($tokens[$prevIndex]->equals('}')) {
                        $isEndingStatement = true;
                    } elseif ($tokens[$prevIndex]->equals(';')) {
                        $beforeSemicolon = $tokens->getPrevNonWhitespace($prevIndex);

                        if ($beforeSemicolon !== null && $tokens[$beforeSemicolon]->isGivenKind([
                            T_ENDIF,
                            T_ENDFOREACH,
                            T_ENDFOR,
                            T_ENDWHILE,
                            T_ENDSWITCH
                        ])) {
                            $isEndingStatement = true;
                        }
                    } elseif ($tokens[$prevIndex]->isGivenKind([
                        T_ENDIF,
                        T_ENDFOREACH,
                        T_ENDFOR,
                        T_ENDWHILE,
                        T_ENDSWITCH
                    ])) {
                        $isEndingStatement = true;
                    }

                    if ($isEndingStatement) {
                        $whitespaceIndex = $index - 1;

                        if ($whitespaceIndex > $prevIndex && $tokens[$whitespaceIndex]->isGivenKind(T_WHITESPACE)) {
                            if (str_contains($tokens[$whitespaceIndex]->getContent(), "\n")) {
                                $tokens[$whitespaceIndex] = new PhpCsFixer\Tokenizer\Token([T_WHITESPACE, ' ']);
                            }
                        }
                    }
                }
            }
        }

        foreach ($tokens as $index => $token) {
            if ($token->equals('{')) {
                $nextIndex = $tokens->getNextNonWhitespace($index);

                if ($nextIndex !== null && $nextIndex > $index + 1) {
                    $whitespaceIndex = $index + 1;

                    if ($tokens[$whitespaceIndex]->isGivenKind(T_WHITESPACE)) {
                        $content = $tokens[$whitespaceIndex]->getContent();
                        $fixed = preg_replace('/^(\r?\n){2,}/', "\n", $content);

                        if ($fixed !== $content) {
                            $tokens[$whitespaceIndex] = new PhpCsFixer\Tokenizer\Token([T_WHITESPACE, $fixed]);
                        }
                    }
                }
            } elseif ($token->isGivenKind(T_OPEN_TAG)) {
                $firstOpenTagIndex = $tokens->getNextNonWhitespace(0);

                if ($index === 0 && $firstOpenTagIndex !== null && $tokens[$firstOpenTagIndex]->isGivenKind([T_DOC_COMMENT, T_COMMENT])) {
                    continue;
                }

                $nextIndex = $tokens->getNextNonWhitespace($index);

                if ($nextIndex !== null && $tokens[$nextIndex]->isGivenKind([
                    T_IF,
                    T_FOREACH,
                    T_FOR,
                    T_WHILE,
                    T_SWITCH,
                    T_TRY,
                    T_ENDIF,
                    T_ENDFOREACH,
                    T_ENDFOR,
                    T_ENDWHILE,
                    T_ENDSWITCH
                ])) {
                    $openTagContent = $token->getContent();
                    $hasNewline = str_contains($openTagContent, "\n");

                    for ($w = $index + 1; $w < $nextIndex; $w++) {
                        if ($tokens[$w]->isGivenKind(T_WHITESPACE) && str_contains($tokens[$w]->getContent(), "\n")) {
                            $hasNewline = true;
                        }
                    }

                    if ($hasNewline) {
                        $tokens[$index] = new PhpCsFixer\Tokenizer\Token([T_OPEN_TAG, "<?php "]);

                        for ($w = $index + 1; $w < $nextIndex; $w++) {
                            if ($tokens[$w]->isGivenKind(T_WHITESPACE)) {
                                $tokens->clearAt($w);
                            }
                        }
                    }
                }
            } elseif ($token->isGivenKind(T_ECHO)) {
                $nextIndex = $tokens->getNextMeaningfulToken($index);

                if ($nextIndex !== null && $tokens[$nextIndex]->equals('(')) {
                    if ($nextIndex === $index + 1) {
                        $tokens->insertAt($index + 1, new PhpCsFixer\Tokenizer\Token([T_WHITESPACE, ' ']));
                    } else {
                        $tokens[$index + 1] = new PhpCsFixer\Tokenizer\Token([T_WHITESPACE, ' ']);
                    }
                }
            }
        }
    }
}

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/aksara',
        __DIR__ . '/install',
        __DIR__ . '/modules',
        __DIR__ . '/themes'
    ])
    ->path([
        'Views',
        'views'
    ]);

$customFixer1 = new NoTrailingWhitespaceInInlineHtmlFixer();
$customFixer2 = new ViewPhpTagFormattingFixer();

$config = new PhpCsFixer\Config();
$config->registerCustomFixers([$customFixer1, $customFixer2]);

return $config->setRules([
    '@PSR12' => true,
    'statement_indentation' => false,
    'no_extra_blank_lines' => [
        'tokens' => [
            'curly_brace_block',
            'extra',
            'parenthesis_brace_block',
            'square_brace_block'
        ]
    ],
    $customFixer1->getName() => true,
    $customFixer2->getName() => true,
    'phpdoc_to_comment' => false,
    'no_empty_phpdoc' => false,
    'phpdoc_trim' => false,
    'no_superfluous_phpdoc_tags' => false,
    'array_syntax' => ['syntax' => 'short'],
    'assign_null_coalescing_to_coalesce_equal' => false,
    'binary_operator_spaces' => [
        'default' => 'single_space'
    ],
    'whitespace_after_comma_in_array' => [
        'ensure_single_space' => true
    ],
    'not_operator_with_successor_space' => true,
    // 'echo_tag_syntax' => [
    //     'format' => 'short'
    // ],
    'yoda_style' => [
        'always_move_variable' => true
    ]
])
->setFinder($finder)
->setCacheFile(__DIR__ . '/.php-cs-fixer-views.cache')
->setIndent("    ")
->setLineEnding("\n");
