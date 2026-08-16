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
    function custom_nl2br(string $string = '', int $limit = 1): string
    {
        $string = preg_replace('/(\r?\n){' . ($limit + 1) . ',}/', str_repeat("$1", $limit), $string);

        return nl2br($string);
    }
}

if (! function_exists('is_json')) {
    /**
     * Check if JSON is valid
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
            $data->_token = generate_csrf_token();
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

        // Compress output if client accepts gzip
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
            header('Content-Encoding: gzip');
            $output = gzencode($output, 6); // Level 6 provides a good balance between compression ratio and speed
        }

        exit($output);
    }
}

if (! function_exists('encoding_fixer')) {
    /**
     * Fix malformed UTF-8 characters, possibly incorrectly encoded
     * json return
     */
    function encoding_fixer(mixed $data = [])
    {
        if (is_string($data)) {
            // Fix malformed UTF-8 characters
            $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        } elseif (is_array($data)) {
            foreach ($data as $key => $val) {
                $data[$key] = encoding_fixer($val);
            }
        } elseif (is_object($data)) {
            foreach ($data as $key => $val) {
                $data->$key = encoding_fixer($val);
            }
        }

        return $data;
    }
}

if (! function_exists('format_date')) {
    /**
     * Format datetime string localized according to user language settings.
     */
    function format_date(?string $dateTime = '', string $format = 'long', bool $withTime = false): string
    {
        if (! $dateTime || empty(trim($dateTime))) {
            return '-';
        }

        try {
            $timestamp = is_numeric($dateTime) ? (int) $dateTime : strtotime($dateTime);

            if (! $timestamp) {
                return '-';
            }

            $language = get_userdata('language') ?: 'en';

            if (class_exists('IntlDateFormatter')) {
                if ('full' === strtolower($format)) {
                    $dateStyle = \IntlDateFormatter::FULL;
                } elseif ('long' === strtolower($format)) {
                    $dateStyle = \IntlDateFormatter::LONG;
                } else {
                    $dateStyle = \IntlDateFormatter::MEDIUM;
                }

                $timeStyle = $withTime ? \IntlDateFormatter::SHORT : \IntlDateFormatter::NONE;

                $formatter = new \IntlDateFormatter(
                    $language,
                    $dateStyle,
                    $timeStyle
                );

                $formatted = $formatter->format($timestamp);

                if ($formatted) {
                    return $formatted;
                }
            }

            // Fallback if IntlDateFormatter is unavailable or failed
            $date = new \DateTime();
            $date->setTimestamp($timestamp);

            $dayName = phrase($date->format('l'));
            $monthShort = phrase($date->format('M'));
            $monthFull = phrase($date->format('F'));
            $dayNum = $date->format('d');
            $yearNum = $date->format('Y');
            $timeStr = ($withTime || 'full' === strtolower($format)) ? ' ' . $date->format('H:i') : '';

            if ('full' === strtolower($format)) {
                if ('en' === strtolower(substr($language, 0, 2))) {
                    return $dayName . ', ' . $monthFull . ' ' . $dayNum . ', ' . $yearNum . $timeStr;
                }

                return $dayName . ', ' . $dayNum . ' ' . $monthFull . ' ' . $yearNum . $timeStr;
            }

            if ('long' === strtolower($format)) {
                if ('en' === strtolower(substr($language, 0, 2))) {
                    return $monthFull . ' ' . $dayNum . ', ' . $yearNum . $timeStr;
                }

                return $dayNum . ' ' . $monthFull . ' ' . $yearNum . $timeStr;
            }

            if ('en' === strtolower(substr($language, 0, 2))) {
                return $monthShort . ' ' . $dayNum . ', ' . $yearNum . $timeStr;
            }

            return $dayNum . ' ' . $monthShort . ' ' . $yearNum . $timeStr;
        } catch (\Throwable $e) {
            log_message('error', '[FORMAT_DATE] ' . $e->getMessage());
        }

        return '-';
    }
}

if (! function_exists('time_ago')) {
    /**
     * Convert a datetime string into a "time ago" format.
     *
     * This function calculates the difference between the provided datetime
     * and the current time, returning a human-readable string (e.g., "2 hours ago").
     * If the input is null or empty, it returns a default phrase.
     */
    function time_ago(?string $dateTime = '', bool $short = false, bool $full = true): string
    {
        // Handle null or empty string input
        if (! $dateTime || empty(trim($dateTime))) {
            return phrase('Just now');
        }

        $timeDifference = time() - strtotime($dateTime);

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

                $phraseKey = $labels[$labelKey];

                if ($time > 1) {
                    // Plural
                    $phraseKey .= 's';
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
