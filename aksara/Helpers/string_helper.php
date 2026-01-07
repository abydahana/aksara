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
 * When the signs is coming, those who don't believe at "that time"
 * have only two choices, commit suicide or become brutal.
 */

use Config\Services;

if (! function_exists('truncate')) {
    /**
     * Truncate the string
     */
    function truncate(?string $string = '', ?int $limit = 0, string $pad = '...'): string
    {
        if (! $string) {
            $string = '';
        }

        $string = strip_tags(str_replace('<', ' <', $string));
        $string = trim(preg_replace('/\s\s+/', ' ', $string));

        if ($limit && strlen($string) >= $limit) {
            $string = substr($string, 0, $limit) . $pad;
        }

        return $string;
    }
}

if (! function_exists('custom_nl2br')) {
    /**
     * Limit new line into break
     */
    function custom_nl2br(string $string = ''): string
    {
        return preg_replace('/(<br(?: \\/)?>\\r?\\n?\\r?)(?=\\1\\1)/is', '', nl2br($string));
    }
}

if (! function_exists('is_json')) {
    /**
     * Check if JSON is valid
     *
     * @param   mixed|null $string
     */
    function is_json(string $string = ''): bool
    {
        if ($string && is_string($string)) {
            $string = json_decode($string, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('make_json')) {
    /**
     * Generate the response as JSON format
     */
    function make_json(array|object $data = [], string $filename = ''): string
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', -1);

        if (isset($data->status) && 200 === $data->status) {
            $data->_token = sha1(current_page() . ENCRYPTION_KEY . get_userdata('session_generated'));
        }

        $data = encoding_fixer($data);

        $minifyPattern = [
            '/\>[^\S ]+/s' => '>',      // Strip whitespaces after tags, except space
            '/[^\S ]+\</s' => '<',      // Strip whitespaces before tags, except space
            '/<!--(.|\s)*?-->/' => ''   // Remove HTML comments
        ];

        $output = preg_replace(array_keys($minifyPattern), array_values($minifyPattern), json_encode($data));

        http_response_code(200);

        header('Content-Type: application/json');

        if ($filename) {
            header('Content-Disposition: attachment; filename=' . $filename . (stripos($filename, '.json') === false ? '.json' : null));
        }

        // Add security headers
        header('Permissions-Policy: geolocation=(self "' . base_url() . '")');
        header('Referrer-Policy: same-origin');
        header('Set-Cookie: HttpOnly; Secure');
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');

        exit($output);
    }
}

if (! function_exists('encoding_fixer')) {
    /**
     * Fix malformed UTF-8 characters, possibly incorrectly encoded
     * json return
     *
     * @param   array|string $data
     */
    function encoding_fixer(mixed $data = [])
    {
        if (! is_string($data) || ! is_array($data)) {
            return $data;
        }

        if (is_string($data)) {
            $encoding = mb_detect_encoding($data);
            $data = mb_convert_encoding($data, 'UTF-8', $encoding);
        } elseif (is_array($data)) {
            foreach ($data as $key => $val) {
                $data[$key] = encoding_fixer($val);
            }
        }

        return $data;
    }
}

if (! function_exists('time_ago')) {
    /**
     * Convert a datetime string into a "time ago" format.
     *
     * This function calculates the difference between the provided datetime
     * and the current time, returning a human-readable string (e.g., "2 hours ago").
     * If the input is null or empty, it returns a default phrase.
     *
     * @param string|null $datetime The datetime string to be converted.
     * @param bool $short           Whether to use short format (e.g., "2h" instead of "2 hours").
     * @param bool $full            Whether to return a full detailed string (for complex diffs).
     * @return string               The human-readable time difference or a default message.
     */
    function time_ago(string $datetime = '', bool $short = false, bool $full = true): string
    {
        // Handle null or empty string input
        if (! $datetime || empty(trim($datetime))) {
            return phrase('Just now');
        }

        $timeDifference = time() - strtotime($datetime);

        if ($timeDifference < 30) {
            return phrase('Just now');
        }

        static $conditions = null;
        if (null === $conditions) {
            $conditions = [
                31536000 => ['full' => 'year', 'short' => 'yr'],
                2592000 => ['full' => 'month', 'short' => 'mo'],
                604800 => ['full' => 'week', 'short' => 'week'],
                86400 => ['full' => 'day', 'short' => 'day'],
                3600 => ['full' => 'hour', 'short' => 'hr'],
                60 => ['full' => 'minute', 'short' => 'min'],
                1 => ['full' => 'second', 'short' => 'sec']
            ];
        }

        foreach ($conditions as $seconds => $labels) {
            if ($timeDifference >= $seconds) {
                $time = (int)($timeDifference / $seconds);
                $labelKey = $full ? 'full' : 'short';

                // Handle "Yesterday" special case
                if ($full && 86400 === $seconds && 1 === $time) {
                    return phrase('Yesterday');
                }

                if ($full && 3600 === $seconds && 24 === $time) {
                    return phrase('Yesterday');
                }

                // Pakai suffix _plural hanya jika time > 1
                $phraseKey = $labels[$labelKey];
                if ($time > 1) {
                    $phraseKey .= '_plural';
                }

                $label = phrase($phraseKey);
                $result = $time . ' ' . $label;

                if (! $short) {
                    $result .= ' ' . phrase('ago');
                }

                return $result;
            }
        }

        return phrase('Just now');
    }
}

if (! function_exists('format_slug')) {
    /**
     * Generate slug from given string
     *
     * @param   mixed|null $string
     */
    function format_slug(string $string = ''): string
    {
        $string = strtolower(preg_replace('/[\-\s]+/', '-', preg_replace('/[^A-Za-z0-9-]+/', '-', trim($string))));

        if (! preg_match('/(\d{10})/', $string)) {
            $string = $string;
        }

        return $string;
    }
}

if (! function_exists('valid_hex')) {
    /**
     * Validate if a string is a valid hexadecimal color code.
     *
     * This function checks for both 3-character and 6-character hex formats
     * starting with a hash (#) symbol.
     *
     * @param string|null $string The hex color string to validate.
     * @return bool               Returns true if valid, false otherwise.
     */
    function valid_hex(string $string = ''): bool
    {
        if ($string && preg_match('/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $string)) {
            return true;
        }

        return false;
    }
}

if (! function_exists('number2alpha')) {
    /**
     * Convert an integer to a string of uppercase letters (A-Z, AA-ZZ, etc.).
     *
     * Useful for generating spreadsheet-like column headers or alphabetical indexing.
     *
     * @param int|null $number    The integer to convert (starting from 0).
     * @param string|null $suffix An optional string to append to the result.
     * @return string|null        The resulting alphabetical string with suffix.
     */
    function number2alpha(int $number = 0, string $suffix = ''): string
    {
        for ($alpha = ''; $number >= 0; $number = intval($number / 26) - 1) {
            $alpha = chr($number % 26 + 0x41) . $alpha;
        }

        return $alpha . $suffix;
    }
}

if (! function_exists('alpha2number')) {
    /**
     * Convert a string of uppercase letters back to an integer.
     *
     * Reverses the transformation performed by number2alpha.
     *
     * @param string|null $alpha  The alphabetical string to convert.
     * @param string|null $suffix An optional string to append to the result.
     * @return string             The resulting integer as a string with suffix.
     */
    function alpha2number(string $alpha = '', string $suffix = ''): string
    {
        $length = strlen($alpha);
        $number = 0;

        for ($i = 0; $i < $length; $i++) {
            $number = $number * 26 + ord($alpha[$i]) - 0x40;
        }

        return ($number - 1) . $suffix;
    }
}

if (! function_exists('encrypt')) {
    /**
     * Encrypt a string and encode the result to base64.
     *
     * @param string $passphrase The raw string to be encrypted.
     * @return string            The base64 encoded encrypted string, or empty string if input is empty.
     */
    function encrypt(string $passphrase = ''): string
    {
        if (! $passphrase) {
            return '';
        }

        $encrypter = Services::encrypter();

        return base64_encode($encrypter->encrypt($passphrase));
    }
}

if (! function_exists('decrypt')) {
    /**
     * Decode a base64 string and decrypt the result.
     *
     * @param string $source The base64 encoded encrypted string.
     * @return string        The decrypted raw string, or empty string if input is empty.
     */
    function decrypt(string $source = ''): string
    {
        if (! $source) {
            return '';
        }

        $encrypter = Services::encrypter();

        return $encrypter->decrypt(base64_decode($source));
    }
}
