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

namespace Aksara\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class JsonFormatter implements FilterInterface
{
    /**
     * Paths that should NOT be formatted.
     * Use dot notation for nested keys (e.g., 'results.item_reference').
     */
    private array $_exemptPaths = [
        'results.item_reference',
        'results.columns',
        'results.field_data',
        'results.table_data.field_data'
    ];

    /**
     * The target casing format. Defaults to 'snake_case'.
     * Options: 'snake_case', 'camelCase', 'PascalCase'
     */
    private string $_format = 'snake_case';

    public function before(RequestInterface $request, $arguments = null)
    {
        // No action needed before the request
    }

    /**
     * Intercept JSON responses and convert all keys to the specified format recursively,
     * except for the paths specified in $_exemptPaths.
     * @param null|mixed $arguments
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Check if a format argument was provided (e.g., ['camelCase'])
        if (! empty($arguments) && isset($arguments[0])) {
            $this->_format = $arguments[0];
        } else {
            // Fallback to config.php constant, default to the class property
            if (defined('API_RESPONSE_FORMAT')) {
                $this->_format = API_RESPONSE_FORMAT;
            }
        }

        // Only process if the response is JSON AND it's explicitly marked as an API Client request
        if (stripos($response->getHeaderLine('Content-Type'), 'application/json') !== false && $response->hasHeader('X-Is-Api-Client')) {
            // Remove the flag header so it doesn't leak to the final output
            $response->removeHeader('X-Is-Api-Client');

            $body = $response->getBody();

            if ($body) {
                // Check if the body is gzipped
                $isGzipped = (stripos($response->getHeaderLine('Content-Encoding'), 'gzip') !== false);

                if ($isGzipped) {
                    $body = gzdecode($body);
                }

                // Decode the JSON body
                $data = json_decode($body, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    // Recursively format the keys
                    $formattedData = $this->formatKeys($data);

                    $output = json_encode($formattedData);

                    // Re-compress if it was gzipped originally
                    if ($isGzipped) {
                        $output = gzencode($output, 6);
                    }

                    // Set the modified response body
                    $response->setBody($output);
                }
            }
        }
    }

    /**
     * Recursively convert array keys to the target format.
     * Exposed publicly so it can be used manually if needed.
     *
     * @param mixed  $data        The data to process
     * @param string $currentPath The dot-notation path of the current level
     *
     * @return mixed The processed data
     */
    public function formatKeys(mixed $data, string $currentPath = '')
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        if (! is_array($data)) {
            return $data;
        }

        $result = [];

        foreach ($data as $key => $value) {
            // Build the path for the current key
            $path = ('' === $currentPath ? $key : $currentPath . '.' . $key);

            // Determine if the PARENT path is exempt.
            if (in_array($currentPath, $this->_exemptPaths, true)) {
                $newKey = $key;
            } else {
                // Format the key
                $newKey = $this->_applyCasing($key);
            }

            // Recursively process the value
            if (is_array($value) || is_object($value)) {
                $result[$newKey] = $this->formatKeys($value, $path);
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    /**
     * Apply the configured casing format to a string.
     */
    private function _applyCasing(string $key): string
    {
        // First, normalize to snake_case as a base if it's currently camelCase
        $normalized = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));

        switch ($this->_format) {
            case 'camelCase':
                // Convert snake_case to camelCase
                return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $normalized))));

            case 'PascalCase':
                // Convert snake_case to PascalCase
                return str_replace(' ', '', ucwords(str_replace('_', ' ', $normalized)));

            case 'snake_case':
            default:
                // Keep as snake_case
                return $normalized;
        }
    }
}
