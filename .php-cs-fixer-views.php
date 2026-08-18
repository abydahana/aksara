<?php

declare(strict_types=1);

class BlankLineAfterFirstPhpClosingTagFixer extends PhpCsFixer\AbstractFixer
{
    public function getName(): string
    {
        return 'Aksara/blank_line_after_first_php_closing_tag';
    }

    public function getDefinition(): PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new PhpCsFixer\FixerDefinition\FixerDefinition(
            'Adds exactly one blank line after the first PHP closing tag in view files.',
            []
        );
    }

    public function isCandidate(PhpCsFixer\Tokenizer\Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_CLOSE_TAG);
    }

    protected function applyFix(\SplFileInfo $file, PhpCsFixer\Tokenizer\Tokens $tokens): void
    {
        $firstTokenIndex = $tokens->getNextNonWhitespace(0);

        if ($firstTokenIndex === null || ! $tokens[$firstTokenIndex]->isGivenKind(T_OPEN_TAG)) {
            return;
        }

        foreach ($tokens as $index => $token) {
            if ($index > $firstTokenIndex && $token->isGivenKind(T_CLOSE_TAG)) {
                if (isset($tokens[$index + 1]) && $tokens[$index + 1]->isGivenKind(T_INLINE_HTML)) {
                    $htmlContent = $tokens[$index + 1]->getContent();

                    if (! str_starts_with($htmlContent, "\n") && ! str_starts_with($htmlContent, "\r\n")) {
                        $tokens[$index + 1] = new PhpCsFixer\Tokenizer\Token([T_INLINE_HTML, "\n" . $htmlContent]);
                    }
                }
                break;
            }
        }
    }
}

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
            'Formats PHP opening/closing tags in view files.',
            []
        );
    }

    public function getPriority(): int
    {
        return -20;
    }

    public function isCandidate(PhpCsFixer\Tokenizer\Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_OPEN_TAG, T_CLOSE_TAG]);
    }

    protected function applyFix(\SplFileInfo $file, PhpCsFixer\Tokenizer\Tokens $tokens): void
    {
        for ($index = count($tokens) - 1; $index >= 0; $index--) {
            if ($tokens[$index]->isGivenKind(T_CLOSE_TAG)) {
                $prevIndex = $tokens->getPrevNonWhitespace($index);

                if ($prevIndex !== null && $tokens[$prevIndex]->equals('}')) {
                    $whitespaceIndex = $index - 1;

                    if ($whitespaceIndex > $prevIndex && $tokens[$whitespaceIndex]->isGivenKind(T_WHITESPACE)) {
                        if (str_contains($tokens[$whitespaceIndex]->getContent(), "\n")) {
                            $tokens[$whitespaceIndex] = new PhpCsFixer\Tokenizer\Token([T_WHITESPACE, ' ']);
                        }
                    }
                }
            }
        }

        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_OPEN_TAG)) {
                $firstOpenTagIndex = $tokens->getNextNonWhitespace(0);

                if ($index === 0 && $firstOpenTagIndex !== null && $tokens[$firstOpenTagIndex]->isGivenKind([T_DOC_COMMENT, T_COMMENT])) {
                    continue;
                }

                $nextIndex = $tokens->getNextNonWhitespace($index);

                if ($nextIndex !== null && $tokens[$nextIndex]->isGivenKind([T_IF, T_FOREACH, T_FOR, T_WHILE, T_SWITCH, T_TRY])) {
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

$customFixer1 = new BlankLineAfterFirstPhpClosingTagFixer();
$customFixer2 = new NoTrailingWhitespaceInInlineHtmlFixer();
$customFixer3 = new ViewPhpTagFormattingFixer();

$config = new PhpCsFixer\Config();
$config->registerCustomFixers([$customFixer1, $customFixer2, $customFixer3]);

return $config->setRules([
    '@PSR12' => true,
    'statement_indentation' => false,
    $customFixer1->getName() => true,
    $customFixer2->getName() => true,
    $customFixer3->getName() => true,
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
