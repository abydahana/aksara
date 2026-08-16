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

use AksaraAI\AIManager;
use AksaraAI\ValueObjects\AIResponse;
use Aksara\Libraries\PageBuilder\PageBuilder;
use Aksara\Laboratory\Model;
use Config\Services;
use Throwable;

class AI
{
    private array $_config = [];

    public function __construct(array|object|null $config = null)
    {
        $this->_config = $config ? (array) $config : $this->_settings();
    }

    /**
     * Override the active AI configuration.
     */
    public function setConfig(array|object $config): static
    {
        $this->_config = (array) $config;

        return $this;
    }

    /**
     * Generate values for multiple Aksara CRUD form fields.
     */
    public function fillForm(string $instruction, array $fields, array $options = []): array
    {
        $schema = [];

        foreach ($fields as $field) {
            if (empty($field['name'])) {
                continue;
            }

            if ($this->_isSensitiveField((string) $field['name'])) {
                continue;
            }

            $type = (string) ($field['type'] ?? 'text');
            $previousFields = $options['previous_fields'] ?? [];
            $previousValue = is_array($previousFields) && array_key_exists((string) $field['name'], $previousFields)
                ? $previousFields[(string) $field['name']]
                : null;

            $schema[] = [
                'name' => (string) $field['name'],
                'label' => (string) ($field['label'] ?? $field['name']),
                'type' => $type,
                'required' => ! empty($field['required']),
                'current_value' => 'pagebuilder' === strtolower($type) ? $this->_pageBuilderSummary((string) ($field['value'] ?? '')) : (string) ($field['value'] ?? ''),
                'previous_ai_value' => null !== $previousValue ? (string) $previousValue : null,
                'options' => $field['options'] ?? []
            ];
        }

        if (empty($options['languages'])) {
            $options['languages'] = $this->_languages();
        }

        $context = new Context($options, $schema, (array) ($options['custom_context'] ?? []));
        $prompt = $context->fillFormPrompt($instruction);
        $response = $this->_complete($prompt, array_merge($options, [
            'max_tokens' => max((int) ($options['max_tokens'] ?? 0), $context->maxTokens()),
            'system_prompt' => 'You are Aksara CMS form assistant. You generate structured JSON values for existing CRUD form fields. Return valid JSON only, with no markdown fences or commentary.'
        ]));

        $response['fields'] = $this->_normalizeResponseFields(
            $this->_jsonObject($response['content'] ?? ''),
            $schema
        );
        $response['fields'] = $this->_filterFields($response['fields'], $schema);
        $this->_sanitizeFields($response['fields'], $schema);
        $this->_normalizePageBuilderFields($response['fields'], $schema);
        $response['labels'] = $this->_labels($response['fields'], $options);
        $this->_generateImageFields($response, $schema, $instruction, $options);

        return $response;
    }

    /**
     * Translate CMS content into the requested language.
     */
    public function translate(string $content, string $language, array $options = []): array
    {
        $prompt = (new Context($options))->translatePrompt($content, $language);

        return $this->_complete($prompt, array_merge($options, [
            'system_prompt' => 'You are Aksara CMS translation assistant. Translate only the supplied CMS content. Preserve structure and return translated content only.'
        ]));
    }

    private function _pageBuilderSummary(string $content): string
    {
        $layout = json_decode($content, true);

        if (! is_array($layout)) {
            return '';
        }

        $counts = [];
        $this->_countPageBuilderComponents($layout['components'] ?? [], $counts);

        return json_encode([
            'version' => $layout['version'] ?? '1.0',
            'framework' => $layout['framework'] ?? 'bootstrap5',
            'component_count' => array_sum($counts),
            'component_types' => $counts
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '';
    }

    private function _countPageBuilderComponents(array $components, array &$counts): void
    {
        foreach ($components as $component) {
            if (! is_array($component) || empty($component['type'])) {
                continue;
            }

            $type = (string) $component['type'];
            $counts[$type] = ($counts[$type] ?? 0) + 1;

            if (! empty($component['children']) && is_array($component['children'])) {
                $this->_countPageBuilderComponents($component['children'], $counts);
            }
        }
    }

    /**
     * Check whether AI is enabled and minimally configured.
     */
    public function ready(): bool
    {
        return $this->_ready($this->_config());
    }

    private function _complete(string $prompt, array $options = []): array
    {
        try {
            $config = $this->_config($options);

            if (! $this->_ready($config)) {
                return $this->_failure('AI is disabled or not configured.');
            }

            $manager = new AIManager($config);
            $response = $manager->text($config['provider'])->complete($prompt, array_merge($options, [
                'system' => $options['system'] ?? $this->_systemPrompt($options)
            ]));

            return $this->_normalize($response);
        } catch (Throwable $e) {
            log_message('error', 'AI request failed: ' . $e->getMessage());

            return $this->_failure($e->getMessage());
        }
    }

    private function _ready(array $config): bool
    {
        return (bool) $config['enabled'] && '' !== $config['api_key'] && '' !== $config['model'];
    }

    private function _normalize(AIResponse $response): array
    {
        return $response->toArray();
    }

    private function _systemPrompt(array $options = []): string
    {
        return trim((string) ($options['system_prompt'] ?? 'You are Aksara CMS editorial assistant. Help administrators create, translate, improve, and optimize CMS content. Preserve HTML tags, shortcodes, internal links, media paths, placeholders, code snippets, SEO intent, and the source language unless the user explicitly asks for translation. Never provide credentials, secrets, sudo/chmod/chown instructions, shell commands, database destructive commands, filesystem access guidance, or privilege escalation steps. Return content that can be pasted directly into Aksara CMS fields.'));
    }

    private function _failure(string $message): array
    {
        return [
            'status' => 500,
            'message' => $message,
            'content' => '',
            'usage' => null,
            'raw' => null
        ];
    }

    private function _jsonObject(string $content): array
    {
        $content = trim($content);
        $content = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $content) ?? $content;
        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function _filterFields(array $fields, array $schema): array
    {
        $allowed = array_column($schema, 'name');

        return array_filter(
            $fields,
            fn ($value, $key): bool => in_array((string) $key, $allowed, true) && ! $this->_isSensitiveField((string) $key),
            ARRAY_FILTER_USE_BOTH
        );
    }

    private function _normalizeResponseFields(array $fields, array $schema): array
    {
        if (isset($fields['fields']) && is_array($fields['fields'])) {
            $fields = $fields['fields'];
        }

        $pageBuilderField = $this->_pageBuilderFieldName($schema);

        if ($pageBuilderField && $this->_looksLikePageBuilderLayout($fields)) {
            return [
                $pageBuilderField => json_encode($fields, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            ];
        }

        if ($pageBuilderField && isset($fields['layout']) && is_array($fields['layout']) && $this->_looksLikePageBuilderLayout($fields['layout'])) {
            $fields[$pageBuilderField] = json_encode($fields['layout'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            unset($fields['layout']);
        }

        if ($pageBuilderField && isset($fields[$pageBuilderField]) && is_array($fields[$pageBuilderField])) {
            $fields[$pageBuilderField] = json_encode($fields[$pageBuilderField], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return $fields;
    }

    private function _pageBuilderFieldName(array $schema): ?string
    {
        foreach ($schema as $field) {
            $name = (string) ($field['name'] ?? '');
            $type = strtolower((string) ($field['type'] ?? ''));

            if ($name && ('pagebuilder' === $type || 'page_content' === $name)) {
                return $name;
            }
        }

        return null;
    }

    private function _looksLikePageBuilderLayout(array $value): bool
    {
        return isset($value['components']) && is_array($value['components']);
    }

    private function _sanitizeFields(array &$fields, array $schema): void
    {
        $types = array_column($schema, 'type', 'name');

        foreach ($fields as $name => &$value) {
            $type = strtolower((string) ($types[$name] ?? ''));

            if (is_array($value) || str_starts_with((string) $value, 'data:image/')) {
                continue;
            }

            if ('pagebuilder' === $type) {
                continue;
            }

            if (in_array($type, ['wysiwyg', 'textarea'], true)) {
                $value = $this->_sanitizeHtml((string) $value);
            } else {
                $value = $this->_stripDangerousText((string) $value);
            }
        }

        unset($value);
    }

    private function _labels(array &$fields, array $options = []): array
    {
        $labels = [];

        $referenceData = $options['custom_context']['data'] ?? [];

        if (! isset($referenceData['language_id'])) {
            $referenceData['language_id'] = ! empty($options['languages']) ? $options['languages'] : $this->_languages();
        }

        foreach ($fields as $fieldName => $value) {
            $dataset = $referenceData[$fieldName] ?? null;

            if (! is_array($dataset) || ! $dataset) {
                continue;
            }

            $item = $this->_matchDatasetItem($value, $dataset);

            if ($item) {
                $id = $item['id'] ?? $item['key'] ?? $item['value'] ?? null;
                $label = $item['title'] ?? $item['label'] ?? $item['name'] ?? $item['language'] ?? null;

                if (null !== $id) {
                    $fields[$fieldName] = (string) $id;
                }

                if (null !== $label) {
                    $labels[$fieldName] = (string) $label;
                }
            }
        }

        return $labels;
    }

    /**
     * Ensure PageBuilder fields are stored as JSON strings for hidden inputs.
     */
    private function _normalizePageBuilderFields(array &$fields, array $schema): void
    {
        foreach ($schema as $field) {
            if ('pagebuilder' !== strtolower((string) ($field['type'] ?? ''))) {
                continue;
            }

            $name = (string) ($field['name'] ?? '');

            if (! $name || ! isset($fields[$name])) {
                continue;
            }

            if (is_array($fields[$name])) {
                $fields[$name] = json_encode($fields[$name], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

            $layout = json_decode((string) $fields[$name], true);

            if (! is_array($layout)) {
                unset($fields[$name]);

                continue;
            }

            $layout = $this->_sanitizePageBuilderLayout($layout);
            $layoutErrors = $this->_validatePageBuilderLayout($layout);
            $validation = (new PageBuilder())->validate($layout);

            if ($layoutErrors || ! ($validation['valid'] ?? false)) {
                $errors = array_merge($layoutErrors, $validation['errors'] ?? []);
                log_message('warning', 'AI PageBuilder layout rejected: ' . implode('; ', $errors));
                unset($fields[$name]);

                continue;
            }

            $fields[$name] = json_encode($layout, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }

    private function _sanitizePageBuilderLayout(array $layout): array
    {
        $layout['version'] = (string) ($layout['version'] ?? '1.0');
        $layout['framework'] = (string) ($layout['framework'] ?? 'bootstrap5');
        $layout['components'] = $this->_sanitizePageBuilderComponents($layout['components'] ?? []);
        $layout['components'] = $this->_normalizePageBuilderStructure($layout['components']);

        return $layout;
    }

    private function _validatePageBuilderLayout(array $layout): array
    {
        $components = config('PageBuilder')->components ?? [];

        return $this->_validatePageBuilderComponents($layout['components'] ?? [], $components, 'root');
    }

    private function _validatePageBuilderComponents(array $items, array $definitions, string $parentType): array
    {
        $errors = [];

        foreach ($items as $index => $component) {
            $type = (string) ($component['type'] ?? '');

            if (! isset($definitions[$type])) {
                $errors[] = 'Unknown component type "' . $type . '" at ' . $parentType . '[' . $index . '].';

                continue;
            }

            if (! $this->_allowedPageBuilderChild($parentType, $type)) {
                $errors[] = 'Component "' . $type . '" is not allowed inside "' . $parentType . '".';
            }

            $hasChildren = isset($component['children']) && is_array($component['children']) && $component['children'];

            if ($hasChildren && empty($definitions[$type]['children'])) {
                $errors[] = 'Component "' . $type . '" cannot contain children.';
            }

            if ($hasChildren) {
                $errors = array_merge($errors, $this->_validatePageBuilderComponents($component['children'], $definitions, $type));
            }
        }

        return $errors;
    }

    private function _allowedPageBuilderChild(string $parentType, string $type): bool
    {
        return match ($parentType) {
            'root' => in_array($type, ['section', 'hero', 'carousel'], true),
            'section' => 'container' === $type,
            'container' => 'row' === $type,
            'row' => 'column' === $type,
            'column' => 'row' === $type || ! in_array($type, ['section', 'container', 'column'], true),
            default => false
        };
    }

    private function _normalizePageBuilderStructure(array $components, string $parentType = 'root'): array
    {
        $normalized = [];

        foreach ($components as $component) {
            if (! is_array($component) || empty($component['type'])) {
                continue;
            }

            $component = $this->_normalizePageBuilderChildren($component);
            $type = (string) $component['type'];

            if ($this->_allowedPageBuilderChild($parentType, $type)) {
                $normalized[] = $component;

                continue;
            }

            $normalized[] = $this->_wrapPageBuilderComponent($component, $parentType);
        }

        return array_values(array_filter($normalized));
    }

    private function _normalizePageBuilderChildren(array $component): array
    {
        $type = (string) ($component['type'] ?? '');

        if (empty($component['children']) || ! is_array($component['children'])) {
            return $component;
        }

        $component['children'] = $this->_normalizePageBuilderStructure($component['children'], $type);

        return $component;
    }

    private function _wrapPageBuilderComponent(array $component, string $parentType): array
    {
        $type = (string) ($component['type'] ?? '');

        if ('root' === $parentType) {
            if ('container' === $type) {
                return $this->_pageBuilderWrapper('section', [$component]);
            }

            if ('row' === $type) {
                return $this->_pageBuilderWrapper('section', [
                    $this->_pageBuilderWrapper('container', [$component])
                ]);
            }

            if ('column' === $type) {
                return $this->_pageBuilderWrapper('section', [
                    $this->_pageBuilderWrapper('container', [
                        $this->_pageBuilderWrapper('row', [$component])
                    ])
                ]);
            }

            return $this->_pageBuilderWrappedContent($component);
        }

        if ('section' === $parentType) {
            if ('row' === $type) {
                return $this->_pageBuilderWrapper('container', [$component]);
            }

            if ('column' === $type) {
                return $this->_pageBuilderWrapper('container', [
                    $this->_pageBuilderWrapper('row', [$component])
                ]);
            }

            return $this->_pageBuilderWrapper('container', [
                $this->_pageBuilderWrapper('row', [
                    $this->_pageBuilderDefaultColumn([$component])
                ])
            ]);
        }

        if ('container' === $parentType) {
            if ('column' === $type) {
                return $this->_pageBuilderWrapper('row', [$component]);
            }

            return $this->_pageBuilderWrapper('row', [
                $this->_pageBuilderDefaultColumn([$component])
            ]);
        }

        if ('row' === $parentType) {
            return $this->_pageBuilderDefaultColumn([$component]);
        }

        return $component;
    }

    private function _pageBuilderWrappedContent(array $component): array
    {
        return $this->_pageBuilderWrapper('section', [
            $this->_pageBuilderWrapper('container', [
                $this->_pageBuilderWrapper('row', [
                    $this->_pageBuilderDefaultColumn([$component])
                ])
            ])
        ]);
    }

    private function _pageBuilderWrapper(string $type, array $children = []): array
    {
        return [
            'type' => $type,
            'id' => uniqid('ai_' . $type . '_', false),
            'props' => [],
            'children' => $children
        ];
    }

    private function _pageBuilderDefaultColumn(array $children = []): array
    {
        return [
            'type' => 'column',
            'id' => uniqid('ai_column_', false),
            'props' => [
                'size' => [
                    'md' => 12
                ]
            ],
            'children' => $children
        ];
    }

    private function _sanitizePageBuilderComponents(array $components): array
    {
        $clean = [];

        foreach ($components as $component) {
            if (! is_array($component) || empty($component['type'])) {
                continue;
            }

            $item = [
                'type' => preg_replace('/[^a-z0-9_\\-]/i', '', (string) $component['type']),
                'id' => preg_replace('/[^a-z0-9_\\-]/i', '', (string) ($component['id'] ?? uniqid('ai_', false))),
                'props' => $this->_sanitizePageBuilderProps($component['props'] ?? [], (string) $component['type'])
            ];

            if (isset($component['children']) && is_array($component['children'])) {
                $item['children'] = $this->_sanitizePageBuilderComponents($component['children']);
            }

            $clean[] = $item;
        }

        return $clean;
    }

    private function _sanitizePageBuilderProps(array $props, string $componentType = ''): array
    {
        foreach ($props as $key => &$value) {
            if (is_array($value)) {
                $value = $this->_sanitizePageBuilderProps($value, $componentType);
            } elseif (is_string($value)) {
                $value = in_array($key, ['url', 'button_url', 'btn_url', 'src', 'image', 'background'], true)
                    ? $this->_sanitizeUrl($value)
                    : $this->_sanitizeHtml($value);
            }
        }

        unset($value);

        if (in_array($componentType, ['section', 'container', 'row', 'column'], true) && ! empty($props['background']) && $this->_isPlaceholderImage((string) $props['background'])) {
            $props['background'] = '';
        }

        return $props;
    }

    private function _isPlaceholderImage(string $value): bool
    {
        return (bool) preg_match('~/uploads/pages/(thumbs/)?placeholder\\.png$~', parse_url($value, PHP_URL_PATH) ?: $value);
    }

    private function _sanitizeHtml(string $value): string
    {
        $value = preg_replace('/<(script|style|iframe|object|embed|form|input|button|meta|link)[^>]*>.*?<\\/\\1>/is', '', $value) ?? '';
        $value = preg_replace('/\\son[a-z]+\\s*=\\s*("[^"]*"|\'[^\']*\'|[^\\s>]+)/i', '', $value) ?? '';
        $value = preg_replace('/(href|src)\\s*=\\s*([\'"])\\s*(javascript|data|vbscript|file):.*?\\2/i', '$1="#"', $value) ?? '';

        return $this->_stripDangerousText($value);
    }

    private function _stripDangerousText(string $value): string
    {
        return preg_replace('/\\b(sudo|chmod|chown|rm\\s+-rf|DROP\\s+TABLE|TRUNCATE\\s+TABLE|\\.env|passwd|shadow|private[_-]?key)\\b/i', '[redacted]', $value) ?? '';
    }

    private function _sanitizeUrl(string $value): string
    {
        $value = trim($this->_stripDangerousText($value));

        if (preg_match('/^(javascript|vbscript|file):/i', $value)) {
            return '#';
        }

        return $value;
    }

    private function _isSensitiveField(string $name): bool
    {
        return (bool) preg_match('/(^|_)(password|passwd|secret|token|api[_-]?key|private[_-]?key|client[_-]?secret|access[_-]?key|auth|credential)(_|$)/i', $name);
    }

    private function _matchDatasetItem(mixed $value, array $items): ?array
    {
        $value = strtolower(trim((string) $value));

        if ('' === $value || ! $items) {
            return null;
        }

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $id = strtolower((string) ($item['id'] ?? $item['key'] ?? $item['value'] ?? ''));
            $title = strtolower((string) ($item['title'] ?? $item['label'] ?? $item['name'] ?? $item['language'] ?? ''));
            $slug = strtolower((string) ($item['slug'] ?? $item['code'] ?? ''));
            $locale = strtolower((string) ($item['locale'] ?? ''));

            if ($value === $id || ($title && $value === $title) || ($slug && $value === $slug) || ($locale && str_contains($locale, $value))) {
                return $item;
            }
        }

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $title = strtolower((string) ($item['title'] ?? $item['label'] ?? $item['name'] ?? $item['language'] ?? ''));
            $slug = strtolower((string) ($item['slug'] ?? $item['code'] ?? ''));
            $description = strtolower((string) ($item['description'] ?? ''));

            if (($title && str_contains($value, $title)) || ($slug && str_contains($value, $slug)) || ($description && str_contains($description, $value))) {
                return $item;
            }
        }

        return null;
    }

    private function _settings(): array
    {
        return [
            'enabled' => (bool) get_setting('ai_enabled'),
            'provider' => get_setting('ai_provider') ?: 'openai',
            'api_key' => $this->_decrypt((string) get_setting('ai_api_key')),
            'model' => get_setting('ai_model') ?: 'gpt-5.6',
            'base_url' => get_setting('ai_base_url') ?: '',
            'temperature' => get_setting('ai_temperature') ?: '0.7',
            'max_tokens' => get_setting('ai_max_tokens') ?: '2048',
            'image_enabled' => (bool) get_setting('ai_image_enabled'),
            'image_model' => get_setting('ai_image_model') ?: 'gpt-image-2',
            'timeout' => 180,
            'connect_timeout' => 15,
            'title' => get_setting('app_name') ?: 'Aksara CMS'
        ];
    }

    private function _config(array $options = []): array
    {
        $config = array_merge($this->_config, $options);
        $config['enabled'] = (bool) ($config['enabled'] ?? false);
        $config['provider'] = strtolower((string) ($config['provider'] ?? 'openai'));
        $config['api_key'] = trim((string) ($config['api_key'] ?? ''));
        $config['model'] = trim((string) ($config['model'] ?? 'gpt-5.6'));
        $config['base_url'] = rtrim((string) ($config['base_url'] ?? ''), '/');
        $config['temperature'] = (float) ($config['temperature'] ?? 0.7);
        $config['max_tokens'] = (int) ($config['max_tokens'] ?? 2048);
        $config['timeout'] = (int) ($config['timeout'] ?? 180);
        $config['connect_timeout'] = (int) ($config['connect_timeout'] ?? 15);
        $config['image_enabled'] = (bool) ($config['image_enabled'] ?? false);
        $config['image_model'] = trim((string) ($config['image_model'] ?? 'gpt-image-2'));

        return $config;
    }

    private function _generateImageFields(array &$response, array $schema, string $instruction, array $options): void
    {
        $config = $this->_config($options);

        if (! $config['image_enabled'] || 'openai' !== $config['provider']) {
            return;
        }

        $imageFields = array_filter($schema, static function ($field): bool {
            $type = strtolower((string) ($field['type'] ?? ''));
            $name = strtolower((string) ($field['name'] ?? ''));

            return in_array($type, ['image-upload', 'image', 'file'], true) || str_contains($name, 'image') || str_contains($name, 'cover');
        });

        if (! $imageFields || ! isset($response['fields']) || ! is_array($response['fields'])) {
            return;
        }

        $manager = new AIManager($config);
        $provider = $manager->image($config['provider']);

        if (! method_exists($provider, 'generateImage')) {
            return;
        }

        foreach ($imageFields as $field) {
            $name = (string) $field['name'];
            $prompt = trim((string) ($response['fields'][$name] ?? ''));

            if (! $prompt || str_starts_with($prompt, 'data:image/')) {
                $prompt = $this->_imagePrompt($field, $response['fields'], $instruction);
            }

            if (! $prompt) {
                continue;
            }

            $image = $provider->generateImage(
                'Create a CMS-ready editorial image for "' . ($field['label'] ?? $name) . "\".\n"
                . "Administrator instruction: " . $instruction . "\n"
                . "Image direction: " . $prompt . "\n"
                . "Avoid text, logos, watermarks, UI chrome, and distorted people. Use a polished editorial composition.",
                [
                    'image_model' => $config['image_model'],
                    'size' => '1024x768'
                ]
            );

            if (200 === ($image['status'] ?? 500) && ! empty($image['image'])) {
                $response['fields'][$name] = $image['image'];
                $response['labels'][$name] = $field['label'] ?? $name;
            } else {
                $message = trim((string) ($image['message'] ?? 'AI image generation failed.'));
                $response['image_errors'][$name] = $message;
                log_message('warning', 'AI image generation failed for field "{field}": {message}', [
                    'field' => $name,
                    'message' => $message
                ]);
            }
        }
    }

    private function _imagePrompt(array $field, array $fields, string $instruction): string
    {
        $parts = array_filter([
            trim((string) $instruction),
            trim((string) ($fields['post_title'] ?? $fields['page_title'] ?? $fields['title'] ?? '')),
            trim(strip_tags((string) ($fields['post_excerpt'] ?? $fields['page_description'] ?? $fields['description'] ?? ''))),
            trim(strip_tags(mb_substr((string) ($fields['post_content'] ?? ''), 0, 600)))
        ]);

        if (! $parts) {
            return '';
        }

        return 'Editorial cover image for ' . ($field['label'] ?? $field['name'] ?? 'image') . '. '
            . implode(' ', $parts);
    }

    /**
     * Decrypt encrypted form values, returning the original value when plain.
     */
    private function _decrypt(string $value): string
    {
        if (! $value) {
            return '';
        }

        try {
            return Services::encrypter()->decrypt(base64_decode($value, true));
        } catch (Throwable $e) {
            return $value;
        }
    }

    private function _languages(): array
    {
        $model = new Model();

        if (! $model->tableExists('app_languages')) {
            return [];
        }

        return array_map(static function ($language) {
            return [
                'id' => (int) $language->id,
                'language' => (string) $language->language,
                'code' => (string) $language->code,
                'locale' => (string) $language->locale
            ];
        }, $model->getWhere('app_languages', ['status' => 1])->result());
    }
}
