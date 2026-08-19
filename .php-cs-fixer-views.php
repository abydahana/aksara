<?php

/**
 * This file is part of Aksara CMS, both framework and publishing
 * platform.
 *
 * @author     Aby Dahana <abydahana@gmail.com>
 * @copyright  (c) Aksara Laboratory <https://aksaracms.com>
 * @license    MIT License
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.txt file.
 *
 * When the signs come, those who don't believe at "that time"
 * will have only two choices, commit suicide or become brutal.
 */

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
                $fixed = preg_replace('/^(\r?\n)+[ \t]*"/', '"', $fixed);

                if ($fixed !== $content) {
                    if ('' === $fixed) {
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
        return -50;
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
                $isHeaderCloseTag = false;
                $isOpeningBlockTag = false;
                $isClosingBlockTag = false;

                $hasPriorHtml = false;
                for ($h = 0; $h < $index; $h++) {
                    if ($tokens[$h]->isGivenKind(T_INLINE_HTML) && '' !== trim($tokens[$h]->getContent())) {
                        $hasPriorHtml = true;
                        break;
                    }
                }

                $blockDepth = 0;
                for ($h = 0; $h < $index; $h++) {
                    if ($tokens[$h]->equals('{')) {
                        $blockDepth++;
                    } elseif ($tokens[$h]->equals('}')) {
                        $blockDepth--;
                    } elseif ($tokens[$h]->equals(':')) {
                        $p = $tokens->getPrevNonWhitespace($h);
                        if (null !== $p && $tokens[$p]->equals(')')) {
                            $blockDepth++;
                        }
                    } elseif ($tokens[$h]->isGivenKind([T_ENDIF, T_ENDFOREACH, T_ENDFOR, T_ENDWHILE, T_ENDSWITCH])) {
                        $blockDepth--;
                    }
                }

                if (! $hasPriorHtml && $blockDepth <= 0) {
                    $isHeaderCloseTag = true;
                } else {
                    if (null !== $prevIndex) {
                        if ($tokens[$prevIndex]->isGivenKind([T_DOC_COMMENT, T_COMMENT])) {
                            $isHeaderCloseTag = true;
                        } elseif ($tokens[$prevIndex]->equals(':')) {
                            $isOpeningBlockTag = true;
                        } elseif ($tokens[$prevIndex]->equals('}')) {
                            $isClosingBlockTag = true;
                        } elseif ($tokens[$prevIndex]->equals(';')) {
                            $beforeSemicolon = $tokens->getPrevNonWhitespace($prevIndex);

                            if (null !== $beforeSemicolon && $tokens[$beforeSemicolon]->isGivenKind([
                                T_ENDIF,
                                T_ENDFOREACH,
                                T_ENDFOR,
                                T_ENDWHILE,
                                T_ENDSWITCH
                            ])) {
                                $isClosingBlockTag = true;
                            }
                        } elseif ($tokens[$prevIndex]->isGivenKind([
                            T_ENDIF,
                            T_ENDFOREACH,
                            T_ENDFOR,
                            T_ENDWHILE,
                            T_ENDSWITCH
                        ])) {
                            $isClosingBlockTag = true;
                        } elseif ($tokens[$prevIndex]->isGivenKind([
                            T_ELSE,
                            T_ELSEIF
                        ])) {
                            $isOpeningBlockTag = true;
                        }
                    }
                }

                $nextIndex = $index + 1;

                if (isset($tokens[$nextIndex]) && $tokens[$nextIndex]->isGivenKind(T_INLINE_HTML)) {
                    $htmlContent = $tokens[$nextIndex]->getContent();
                    $trimmedHtml = ltrim($htmlContent, " \t\r\n");

                    if (str_starts_with($trimmedHtml, '"') || str_starts_with($trimmedHtml, "'")) {
                        $tokens[$index] = new PhpCsFixer\Tokenizer\Token([T_CLOSE_TAG, '?>']);

                        if ($trimmedHtml !== $htmlContent) {
                            if ('' === $trimmedHtml) {
                                $tokens->clearAt($nextIndex);
                            } else {
                                $tokens[$nextIndex] = new PhpCsFixer\Tokenizer\Token([T_INLINE_HTML, $trimmedHtml]);
                            }
                        }
                    } else {
                        if ($isHeaderCloseTag) {
                            $tokens[$index] = new PhpCsFixer\Tokenizer\Token([T_CLOSE_TAG, "?>\n"]);

                            $whitespaceIndex = $index - 1;
                            if ($whitespaceIndex >= 0 && $tokens[$whitespaceIndex]->isGivenKind(T_WHITESPACE)) {
                                if (! str_contains($tokens[$whitespaceIndex]->getContent(), "\n")) {
                                    $tokens[$whitespaceIndex] = new PhpCsFixer\Tokenizer\Token([T_WHITESPACE, "\n"]);
                                }
                            }

                            if (! preg_match('/^\r?\n/', $htmlContent)) {
                                $fixedHtml = "\n" . $htmlContent;
                                $tokens[$nextIndex] = new PhpCsFixer\Tokenizer\Token([T_INLINE_HTML, $fixedHtml]);
                            } elseif (preg_match('/^(\r?\n){2,}/', $htmlContent)) {
                                $fixedHtml = preg_replace('/^(\r?\n){2,}/', "\n", $htmlContent);
                                $tokens[$nextIndex] = new PhpCsFixer\Tokenizer\Token([T_INLINE_HTML, $fixedHtml]);
                            }
                        } elseif ($isOpeningBlockTag) {
                            if (preg_match('/^\r?\n/', $htmlContent)) {
                                $fixedHtml = preg_replace('/^(\r?\n)+/', '', $htmlContent);

                                if ($fixedHtml !== $htmlContent) {
                                    if ('' === $fixedHtml) {
                                        $tokens->clearAt($nextIndex);
                                    } else {
                                        $tokens[$nextIndex] = new PhpCsFixer\Tokenizer\Token([T_INLINE_HTML, $fixedHtml]);
                                    }
                                }
                            }
                        } elseif ($isClosingBlockTag) {
                            $isFollowedByClosingHtmlTag = str_starts_with($trimmedHtml, '</');
                            $isFollowedByClosingBlockTag = false;

                            $nextOpenTag = $tokens->getNextTokenOfKind($index, [[T_OPEN_TAG], [T_OPEN_TAG_WITH_ECHO]]);

                            if (null !== $nextOpenTag) {
                                $afterOpen = $tokens->getNextMeaningfulToken($nextOpenTag);

                                if (null !== $afterOpen && $tokens[$afterOpen]->isGivenKind([
                                    T_ELSE,
                                    T_ELSEIF,
                                    T_ENDIF,
                                    T_ENDFOREACH,
                                    T_ENDFOR,
                                    T_ENDWHILE,
                                    T_ENDSWITCH
                                ])) {
                                    $isFollowedByClosingBlockTag = true;
                                }
                            }

                            if ($isFollowedByClosingHtmlTag || $isFollowedByClosingBlockTag) {
                                if (preg_match('/^\r?\n/', $htmlContent)) {
                                    $fixedHtml = preg_replace('/^(\r?\n)+/', '', $htmlContent);

                                    if ($fixedHtml !== $htmlContent) {
                                        if ('' === $fixedHtml) {
                                            $tokens->clearAt($nextIndex);
                                        } else {
                                            $tokens[$nextIndex] = new PhpCsFixer\Tokenizer\Token([T_INLINE_HTML, $fixedHtml]);
                                        }
                                    }
                                }
                            } else {
                                if (! preg_match('/^\r?\n/', $htmlContent)) {
                                    $fixedHtml = "\n" . $htmlContent;
                                    $tokens[$nextIndex] = new PhpCsFixer\Tokenizer\Token([T_INLINE_HTML, $fixedHtml]);
                                } elseif (preg_match('/^(\r?\n){3,}/', $htmlContent)) {
                                    preg_match('/[ \t]*$/', $htmlContent, $indentMatch);
                                    $indent = $indentMatch[0] ?? '';
                                    $fixedHtml = preg_replace('/^(\r?\n){3,}[ \t]*/', "\n\n" . $indent, $htmlContent);
                                    $tokens[$nextIndex] = new PhpCsFixer\Tokenizer\Token([T_INLINE_HTML, $fixedHtml]);
                                }
                            }
                        } else {
                            $openIndex = $tokens->getPrevTokenOfKind($index, [[T_OPEN_TAG], [T_OPEN_TAG_WITH_ECHO]]);
                            $isOpenTagOnNewLine = false;

                            if (null !== $openIndex && $openIndex > 0 && $tokens[$openIndex - 1]->isGivenKind(T_INLINE_HTML)) {
                                if (preg_match('/\r?\n[ \t]*$/', $tokens[$openIndex - 1]->getContent())) {
                                    $isOpenTagOnNewLine = true;
                                }
                            }

                            if ($isOpenTagOnNewLine) {
                                if (preg_match('/^(\r?\n){2,}/', $htmlContent)) {
                                    preg_match('/[ \t]*$/', $htmlContent, $indentMatch);
                                    $indent = $indentMatch[0] ?? '';
                                    $fixedHtml = preg_replace('/^(\r?\n){2,}[ \t]*/', "\n\n" . $indent, $htmlContent);

                                    if ($fixedHtml !== $htmlContent) {
                                        $tokens[$nextIndex] = new PhpCsFixer\Tokenizer\Token([T_INLINE_HTML, $fixedHtml]);
                                    }
                                }
                            } else {
                                if (preg_match('/^\r?\n/', $htmlContent)) {
                                    $fixedHtml = preg_replace('/^(\r?\n)+/', '', $htmlContent);

                                    if ($fixedHtml !== $htmlContent) {
                                        if ('' === $fixedHtml) {
                                            $tokens->clearAt($nextIndex);
                                        } else {
                                            $tokens[$nextIndex] = new PhpCsFixer\Tokenizer\Token([T_INLINE_HTML, $fixedHtml]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if ($isOpeningBlockTag || $isClosingBlockTag) {
                    $whitespaceIndex = $index - 1;

                    if ($whitespaceIndex > $prevIndex && $tokens[$whitespaceIndex]->isGivenKind(T_WHITESPACE)) {
                        if (str_contains($tokens[$whitespaceIndex]->getContent(), "\n")) {
                            $tokens[$whitespaceIndex] = new PhpCsFixer\Tokenizer\Token([T_WHITESPACE, ' ']);
                        }
                    }
                }
            } elseif ($tokens[$index]->isGivenKind([T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO])) {
                $prevIndex = $index - 1;

                if ($prevIndex >= 0 && $tokens[$prevIndex]->isGivenKind(T_INLINE_HTML)) {
                    $htmlContent = $tokens[$prevIndex]->getContent();

                    $nextMeaningful = $tokens->getNextMeaningfulToken($index);
                    $isClosingStatementToken = false;

                    if (null !== $nextMeaningful) {
                        if ($tokens[$nextMeaningful]->equals('}') || $tokens[$nextMeaningful]->isGivenKind([
                            T_ENDIF,
                            T_ENDFOREACH,
                            T_ENDFOR,
                            T_ENDWHILE,
                            T_ENDSWITCH,
                            T_ELSE,
                            T_ELSEIF
                        ])) {
                            $isClosingStatementToken = true;
                        }
                    }

                    if ($isClosingStatementToken) {
                        if (preg_match('/(\r?\n){2,}[ \t]*$/', $htmlContent)) {
                            preg_match('/[ \t]*$/', $htmlContent, $indentMatch);
                            $indent = $indentMatch[0] ?? '';

                            $fixedHtml = preg_replace('/(\r?\n)+[ \t]*$/', "\n" . $indent, $htmlContent);

                            if ($fixedHtml !== $htmlContent) {
                                $tokens[$prevIndex] = new PhpCsFixer\Tokenizer\Token([T_INLINE_HTML, $fixedHtml]);
                            }
                        }
                    } else {
                        $isEchoTag = $tokens[$index]->isGivenKind(T_OPEN_TAG_WITH_ECHO);
                        $inlinePattern = '/(<\/(i|span|b|strong|small|code|em)>)\s*\r?\n+\s*$/i';

                        if ($isEchoTag && preg_match($inlinePattern, $htmlContent)) {
                            $fixedHtml = preg_replace($inlinePattern, '$1 ', $htmlContent);

                            if ($fixedHtml !== $htmlContent) {
                                $tokens[$prevIndex] = new PhpCsFixer\Tokenizer\Token([T_INLINE_HTML, $fixedHtml]);
                            }
                        } else {
                            $blockPattern = '/<\/(div|p|section|h[1-6]|ul|ol|li|form|header|footer|nav|article|aside|blockquote|table|tbody|thead|tr|td|th)>[ \t]*\r?\n$/i';

                            if (preg_match($blockPattern, $htmlContent)) {
                                preg_match('/[ \t]*$/', $htmlContent, $indentMatch);
                                $indent = $indentMatch[0] ?? '';

                                $fixedHtml = preg_replace('/(<\/(div|p|section|h[1-6]|ul|ol|li|form|header|footer|nav|article|aside|blockquote|table|tbody|thead|tr|td|th)>)\s*$/i', '$1' . "\n\n" . $indent, $htmlContent);

                                if ($fixedHtml !== $htmlContent) {
                                    $tokens[$prevIndex] = new PhpCsFixer\Tokenizer\Token([T_INLINE_HTML, $fixedHtml]);
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($tokens as $index => $token) {
            if ($token->equals('{')) {
                $nextMeaningful = $tokens->getNextMeaningfulToken($index);

                if (null !== $nextMeaningful && $tokens[$nextMeaningful]->isGivenKind(T_CLOSE_TAG)) {
                    for ($w = $index + 1; $w < $nextMeaningful; $w++) {
                        if ($tokens[$w]->isGivenKind(T_WHITESPACE)) {
                            if (str_contains($tokens[$w]->getContent(), "\n")) {
                                $tokens[$w] = new PhpCsFixer\Tokenizer\Token([T_WHITESPACE, ' ']);
                            }
                        }
                    }
                } elseif (null !== $nextMeaningful && $nextMeaningful > $index + 1) {
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

                if (0 === $index && null !== $firstOpenTagIndex && $tokens[$firstOpenTagIndex]->isGivenKind([T_DOC_COMMENT, T_COMMENT])) {
                    continue;
                }

                $nextIndex = $tokens->getNextNonWhitespace($index);

                if (null !== $nextIndex && $tokens[$nextIndex]->isGivenKind([
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

                if (null !== $nextIndex && $tokens[$nextIndex]->equals('(')) {
                    if ($index + 1 === $nextIndex) {
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
    'single_space_around_construct' => true,
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
