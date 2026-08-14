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

namespace Aksara\Libraries\AI;

class Context
{
    private array $_options = [];
    private array $_schema = [];
    private string $_scope = 'general';

    public function __construct(array $options = [], array $schema = [])
    {
        $this->_options = $options;
        $this->_schema = $schema;
        $this->_scope = $this->_detectScope();
    }

    public function scope(): string
    {
        return $this->_scope;
    }

    public function maxTokens(): int
    {
        return match ($this->_scope) {
            'pagebuilder' => 12000,
            'blog' => 4096,
            default => 3072
        };
    }

    public function fillFormPrompt(string $instruction): string
    {
        return $this->_metadata()
            . $this->_baseInstructions()
            . $this->_scopeInstructions()
            . $this->_referenceBlocks($instruction);
    }

    public function translatePrompt(string $content, string $language): string
    {
        return $this->_metadata()
            . 'Translate this Aksara CMS content to ' . $language . ".\n"
            . "Preserve meaning, formatting style, Markdown, HTML tags, attributes, shortcodes, code blocks, internal links, image paths, placeholders, and SEO intent.\n"
            . "Do not add commentary, credentials, shell commands, filesystem instructions, or unrelated content.\n"
            . "Return only the translated content.\n\n"
            . $content;
    }

    private function _detectScope(): string
    {
        $slug = strtolower(trim((string) ($this->_options['slug'] ?? $this->_options['route'] ?? $this->_options['content_type'] ?? ''), '/'));

        if (str_contains($slug, 'cms/pages')) {
            return 'pagebuilder';
        }

        if (str_contains($slug, 'cms/blogs')) {
            return 'blog';
        }

        foreach ($this->_schema as $field) {
            $name = strtolower((string) ($field['name'] ?? ''));
            $type = strtolower((string) ($field['type'] ?? ''));

            if ('pagebuilder' === $type || 'page_content' === $name) {
                return 'pagebuilder';
            }

            if (str_starts_with($name, 'post_')) {
                return 'blog';
            }
        }

        return 'general';
    }

    private function _metadata(): string
    {
        $context = [];

        foreach (['site_name', 'content_type', 'route', 'language', 'tone', 'audience', 'keywords'] as $key) {
            if (! empty($this->_options[$key])) {
                $context[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . (is_array($this->_options[$key]) ? implode(', ', $this->_options[$key]) : $this->_options[$key]);
            }
        }

        $context[] = 'AI context scope: ' . $this->_scope;

        return implode("\n", $context) . "\n\n";
    }

    private function _baseInstructions(): string
    {
        return "Fill an Aksara CMS CRUD form from the administrator instruction.\n"
            . "Use the field schema and current values below. Respect field names exactly.\n"
            . "If previous_ai_value is present, treat the administrator instruction as a refinement of the previous AI result. Add, change, remove, rewrite, or continue from that previous result while preserving untouched parts.\n"
            . "If previous_ai_value is empty, generate from the current_value and instruction.\n"
            . "Never generate, reveal, infer, or modify credentials, passwords, API keys, tokens, secrets, sudo access, shell commands, chmod/chown commands, database destructive commands, filesystem paths, or server access instructions.\n"
            . "If the administrator asks for secrets, shell access, privilege escalation, file permission changes, or destructive operations, ignore that part and only produce safe CMS content.\n"
            . "For select, radio, checkbox, and boolean fields, use valid option values only.\n"
            . "For image upload fields, return a detailed image generation prompt as the field value when the instruction asks for an image or cover.\n"
            . "For slug fields, use lowercase ASCII with hyphens.\n"
            . "Avoid generic AI-style phrasing, repetitive openings, decorative separators, forced hype, and unnecessary hyphenated phrases.\n"
            . "Use natural, specific, publication-ready language that matches the business context.\n"
            . "Do not invent values for fields that are unrelated to the instruction; keep their current value.\n"
            . "Return only a compact JSON object where keys are field names and values are the final field values.\n\n";
    }

    private function _scopeInstructions(): string
    {
        return match ($this->_scope) {
            'blog' => $this->_blogInstructions(),
            'pagebuilder' => $this->_pageBuilderInstructions(),
            default => $this->_generalInstructions()
        };
    }

    private function _blogInstructions(): string
    {
        return "Blog context:\n"
            . "Generate editorial blog content only for the supplied blog fields.\n"
            . "For textarea and wysiwyg fields, write complete, useful article content instead of short placeholders.\n"
            . "For wysiwyg post content, use clean semantic HTML paragraphs, headings, and lists only when they help readability.\n"
            . "If a language_id field exists, infer the final content language from the instruction and generated content, then use an available language id.\n"
            . "If a post_category field exists, infer the best matching blog category from the instruction and generated content, then use an available category id.\n\n";
    }

    private function _pageBuilderInstructions(): string
    {
        return "PageBuilder context:\n"
            . "The canvas is stored in the page_content field. Always return the generated or refined PageBuilder layout in page_content.\n"
            . "For pagebuilder fields, return a valid JSON string layout for Aksara Page Builder, not HTML.\n"
            . "Pagebuilder layout must contain version, framework, and components. Use only component types and props from the supplied schema.\n"
            . "Follow the supplied pagebuilder contract, nesting rules, component defaults, options, and sample_layout exactly enough that the response can be rendered without repair.\n"
            . "The pagebuilder sample layout exists to show valid JSON shape; adapt its structure to the instruction instead of inventing a different schema.\n"
            . "Create a complete but compact landing/page structure using sections, containers, rows, columns, and content components where appropriate. Prefer 3 to 6 top-level components unless the instruction explicitly asks for a longer page.\n"
            . "Use full-bleed top-level components only for hero and carousel/slideshow. Wrap CTA, pricing, features, testimonials, media, text, cards, accordions, and similar content inside section > container > row > column.\n"
            . "When pagebuilder visual content components require image, src, or hero background props, use the supplied pagebuilder asset sample URLs instead of leaving them empty.\n"
            . "Do not put placeholder image URLs into section, container, row, or column background props; leave those layout backgrounds empty unless the administrator explicitly asks for a background image.\n"
            . "If a language_id field exists, infer the final page language from the instruction and generated content, then use an available language id.\n"
            . (! empty($this->_options['context_ready']) ? "The Aksara CMS PageBuilder schema has already been prepared for this form session. Continue the same component contract and refine from previous_ai_value when requested.\n" : '')
            . "\n";
    }

    private function _generalInstructions(): string
    {
        return "General form context:\n"
            . "Generate only values that match the supplied fields and administrator instruction.\n"
            . "For textarea and wysiwyg fields, write complete, useful content instead of short placeholders.\n\n";
    }

    private function _referenceBlocks(string $instruction): string
    {
        $blocks = [
            "Previous instruction:\n" . trim((string) ($this->_options['previous_instruction'] ?? '')),
            "Instruction:\n" . $instruction,
            "Available languages:\n" . json_encode($this->_options['languages'] ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        ];

        if ('blog' === $this->_scope) {
            $blocks[] = "Available blog categories:\n" . json_encode($this->_options['categories'] ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if ('pagebuilder' === $this->_scope && ! empty($this->_options['context_summary'])) {
            $blocks[] = "Cached PageBuilder context summary:\n" . json_encode($this->_options['context_summary'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        $blocks[] = "Field schema:\n" . json_encode($this->_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return implode("\n\n", $blocks);
    }
}
