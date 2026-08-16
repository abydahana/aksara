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

namespace Aksara\Modules\XHR\Controllers;

use Aksara\Laboratory\Core;
use Aksara\Libraries\AI\AI as AksaraAI;

class AI extends Core
{
    private array $_actions = [
        'form_fill'
    ];

    public function __construct()
    {
        parent::__construct();

        $this->setPermission();
        $this->permission->mustAjax();
    }

    public function index()
    {
        $this->_extendExecutionTime();

        if ('post' !== strtolower($this->request->getMethod())) {
            return $this->_error(405, phrase('The method you requested is not acceptable.'));
        }

        if (! $this->_rateLimit()) {
            return $this->_error(429, phrase('Too many requests. Please wait a moment and try again.'));
        }

        $action = trim((string) $this->request->getPost('action'));

        if (! in_array($action, $this->_actions, true)) {
            return $this->_error(400, phrase('The selected action is not valid.'));
        }

        $ai = new AksaraAI();

        if (! $ai->ready()) {
            return $this->_error(400, phrase('AI is disabled or not configured.'));
        }

        $route = $this->_contextRoute();
        $options = [
            'site_name' => get_setting('app_name'),
            'content_type' => trim((string) $this->request->getPost('content_type')),
            'route' => $route,
            'language' => trim((string) $this->request->getPost('language')),
            'tone' => trim((string) $this->request->getPost('tone')),
            'audience' => trim((string) $this->request->getPost('audience')),
            'keywords' => $this->_keywords($this->request->getPost('keywords')),
            'limit' => (int) ($this->request->getPost('limit') ?: 160)
        ];
        $title = trim((string) $this->request->getPost('title'));
        $instruction = trim((string) $this->request->getPost('instruction'));
        $fields = $this->_fields($this->request->getPost('fields'));
        $contextKey = $this->_contextKey($fields);
        $context = $this->_context($contextKey);

        if ($this->_unsafeInstruction($instruction . "\n" . $title)) {
            $this->_logBlockedRequest($action);

            return $this->_error(400, phrase('The AI request contains unsafe system or credential instructions.'));
        }

        if (! $this->_validatePayload($instruction, $fields)) {
            return $this->_error(400, phrase('Please complete the required AI fields.'));
        }

        if ($context) {
            $options['previousInstruction'] = $context['instruction'] ?? '';
            $options['previousFields'] = $context['fields'] ?? [];
            $options['contextReady'] = ! empty($context['schemaSignature']);
            $options['contextSummary'] = $context['schemaSummary'] ?? [];
            $fields = $this->_compactContextFields($fields, $context);
        }

        // Merge custom context set by the controller module via setAiContext().
        $customContext = $this->_customContext($route);

        if ($customContext) {
            $options['customContext'] = $customContext;

            // Allow custom context to supply top-level tone/audience overrides
            // when the form request itself did not carry them.
            foreach (['tone', 'audience'] as $key) {
                if (empty($options[$key]) && ! empty($customContext[$key])) {
                    $options[$key] = $customContext[$key];
                }
            }
        }

        $response = $ai->fillForm($instruction, $fields, $options);

        if (($response['status'] ?? 500) < 400 && ! empty($response['fields']) && is_array($response['fields'])) {
            $this->_saveContext($contextKey, $instruction, $response, $fields);
        }

        $this->_logUsage($action, $response);

        return make_json([
            'status' => $response['status'] ?? 500,
            'message' => $response['message'] ?? '',
            'content' => $response['content'] ?? '',
            'fields' => $response['fields'] ?? null,
            'labels' => $response['labels'] ?? null,
            'imageErrors' => $response['imageErrors'] ?? null,
            'refined' => (bool) $context,
            'usage' => $response['usage'] ?? null
        ]);
    }

    private function _validatePayload(string $instruction, array $fields = []): bool
    {
        return (bool) ($instruction && $fields);
    }

    private function _fields(mixed $fields): array
    {
        if (is_array($fields)) {
            return $fields;
        }

        $decoded = json_decode((string) $fields, true);

        return is_array($decoded) ? $decoded : [];
    }



    private function _keywords(mixed $keywords): array|string
    {
        if (is_array($keywords)) {
            return array_filter(array_map('trim', $keywords));
        }

        return trim((string) $keywords);
    }

    private function _rateLimit(): bool
    {
        $cache = service('cache');
        $key = 'aksara_ai_rate_' . (get_userdata('user_id') ?: service('request')->getIPAddress());
        $attempts = (int) ($cache->get($key) ?: 0);

        if ($attempts >= 20) {
            return false;
        }

        $cache->save($key, $attempts + 1, 60);

        return true;
    }

    private function _contextKey(array $fields): string
    {
        $names = array_values(array_filter(array_map(static fn ($field): string => (string) ($field['name'] ?? ''), $fields)));
        sort($names);

        return 'aksara_ai_context_' . md5(implode('|', [
            get_userdata('user_id') ?: service('request')->getIPAddress(),
            $this->_contextRoute(),
            trim((string) $this->request->getPost('content_type')),
            trim((string) $this->request->getPost('context_id')),
            implode(',', $names)
        ]));
    }

    private function _context(string $key): array
    {
        $context = service('cache')->get($key);

        return is_array($context) ? $context : [];
    }

    /**
     * Reads the custom AI context set by the controller module via Core::setAiContext().
     *
     * Keyed per route path — one cache entry per controller, shared across users.
     *
     * @param string $route The resolved referer route.
     */
    private function _customContext(string $route): array
    {
        $cacheKey = 'aksara_ai_custom_context_' . md5($route);
        $context = service('cache')->get($cacheKey);

        return is_array($context) ? $context : [];
    }

    private function _saveContext(string $key, string $instruction, array $response, array $fields): void
    {
        $cache = service('cache');
        $context = [
            'instruction' => $instruction,
            'fields' => $response['fields'] ?? [],
            'labels' => $response['labels'] ?? [],
            'schemaSignature' => $this->_schemaSignature($fields),
            'schemaSummary' => $this->_schemaSummary($fields),
            'createdAt' => time()
        ];

        $cache->save($key, $context, 7200);

        $indexKey = $this->_contextIndexKey();
        $index = $cache->get($indexKey);
        $index = is_array($index) ? $index : [];
        $index[$key] = time();

        $cache->save($indexKey, $index, 7200);
    }

    private function _compactContextFields(array $fields, array $context): array
    {
        return array_map(function ($field) use ($context): array {
            if (! is_array($field)) {
                return $field;
            }

            $type = strtolower((string) ($field['type'] ?? ''));
            $name = strtolower((string) ($field['name'] ?? ''));

            if ('pagebuilder' !== $type && 'page_content' !== $name) {
                return $field;
            }

            $field['options'] = [
                'contextReady' => true,
                'summary' => $context['schemaSummary']['pagebuilder'] ?? []
            ];

            return $field;
        }, $fields);
    }

    private function _schemaSignature(array $fields): string
    {
        return md5(json_encode($this->_schemaSummary($fields), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '');
    }

    private function _schemaSummary(array $fields): array
    {
        $summary = [];

        foreach ($fields as $field) {
            if (! is_array($field)) {
                continue;
            }

            $type = strtolower((string) ($field['type'] ?? ''));
            $name = strtolower((string) ($field['name'] ?? ''));

            if ('pagebuilder' !== $type && 'page_content' !== $name) {
                continue;
            }

            $components = $field['options']['components'] ?? [];
            $cachedSummary = $field['options']['summary'] ?? [];
            $summary['pagebuilder'] = [
                'componentTypes' => is_array($components) && $components ? array_keys($components) : ($cachedSummary['componentTypes'] ?? []),
                'assetSamples' => $field['options']['assetSamples'] ?? ($cachedSummary['assetSamples'] ?? []),
                'layoutShape' => $field['options']['layoutShape'] ?? ($cachedSummary['layoutShape'] ?? []),
                'knownContext' => 'Aksara CMS PageBuilder schema was already prepared for this form session.'
            ];
        }

        return $summary;
    }

    private function _contextRoute(): string
    {
        $referer = (string) $this->request->getServer('HTTP_REFERER');
        $path = trim((string) parse_url($referer, PHP_URL_PATH), '/');
        $basePath = trim((string) parse_url(base_url(), PHP_URL_PATH), '/');

        if ($path && $basePath && str_starts_with($path, $basePath)) {
            $path = trim(substr($path, strlen($basePath)), '/');
        }

        return $path ?: trim((string) uri_string(), '/');
    }

    private function _contextIndexKey(?string $route = null): string
    {
        return 'aksara_ai_context_index_' . md5(implode('|', [
            get_userdata('user_id') ?: service('request')->getIPAddress(),
            $route ?? $this->_contextRoute()
        ]));
    }

    private function _unsafeInstruction(string $text): bool
    {
        $patterns = [
            '/\\b(sudo|chmod|chown|passwd|shadow|ssh-keygen)\\b/i',
            '/\\b(rm\\s+-rf|mkfs|dd\\s+if=|DROP\\s+TABLE|TRUNCATE\\s+TABLE)\\b/i',
            '/\\b(read|show|print|dump|reveal|get|steal)\\b.{0,40}\\b(password|secret|token|api[_-]?key|private[_-]?key|\\.env|credential)\\b/i',
            '/\\b(shell|terminal|command line|filesystem|server root|root access|privilege escalation)\\b/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    private function _logBlockedRequest(string $action): void
    {
        log_message('warning', 'Blocked unsafe AI action "{action}" requested by user #{user_id}.', [
            'action' => $action,
            'user_id' => get_userdata('user_id') ?: 0
        ]);
    }

    private function _extendExecutionTime(): void
    {
        if (function_exists('ini_set')) {
            ini_set('max_execution_time', '180');
        }

        if (function_exists('set_time_limit')) {
            set_time_limit(180);
        }
    }

    private function _logUsage(string $action, array $response): void
    {
        log_message('info', 'AI action "{action}" requested by user #{user_id} using {provider}/{model}. Status: {status}', [
            'action' => $action,
            'user_id' => get_userdata('user_id') ?: 0,
            'provider' => get_setting('ai_provider') ?: 'openai',
            'model' => get_setting('ai_model') ?: '',
            'status' => $response['status'] ?? 500
        ]);
    }

    private function _error(int $status, string $message): string
    {
        return make_json([
            'status' => $status,
            'message' => $message,
            'content' => '',
            'usage' => null
        ]);
    }
}
