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

if (! function_exists('truncate')) {
    /**
     * Truncate the string
     *
     * @param   string $string
     * @param   string $pad
     */
    function truncate($string = null, int $limit = 0, $pad = '...')
    {
        if (! $string) {
            return;
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
     *
     * @param   string $string
     */
    function custom_nl2br($string = '')
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
    function is_json($string = null)
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
     *
     * @param   object|array $data
     * @param   string $filename
     */
    function make_json($data = [], $filename = null)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', -1);

        if (isset($data->status) && 200 === $data->status) {
            $data->_token = sha1(current_page() . ENCRYPTION_KEY . get_userdata('session_generated'));
        }

        $data = encoding_fixer($data);

        $minify_pattern = [
            '/\>[^\S ]+/s' => '>',      // Strip whitespaces after tags, except space
            '/[^\S ]+\</s' => '<',      // Strip whitespaces before tags, except space
            '/<!--(.|\s)*?-->/' => ''   // Remove HTML comments
        ];

        $output = preg_replace(array_keys($minify_pattern), array_values($minify_pattern), json_encode($data));

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
    function encoding_fixer($data = [])
    {
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
     * Convert timestamp to time ago
     *
     * @param   string $datetime
     */
    function time_ago($datetime = null, bool $short = false, bool $full = true)
    {
        if (! $datetime) {
            return phrase('Just now');
        }

        $time_difference = time() - strtotime($datetime);

        if ($time_difference < 30) {
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
            if ($time_difference >= $seconds) {
                $time = (int)($time_difference / $seconds);
                $label_key = $full ? 'full' : 'short';

                // Handle "Yesterday" special case
                if ($full && 86400 === $seconds && 1 === $time) {
                    return phrase('Yesterday');
                }

                if ($full && 3600 === $seconds && 24 === $time) {
                    return phrase('Yesterday');
                }

                // Pakai suffix _plural hanya jika time > 1
                $phrase_key = $labels[$label_key];
                if ($time > 1) {
                    $phrase_key .= '_plural';
                }

                $label = phrase($phrase_key);
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
    function format_slug($string = null)
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
     * Validate hex color
     */
    function valid_hex(string $string = null)
    {
        if ($string && preg_match('/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $string)) {
            return true;
        }

        return false;
    }
}

if (! function_exists('number2alpha')) {
    /*
     * Convert an integer to a string of uppercase letters (A-Z, AA-ZZ, AAA-ZZZ, etc.)
     */
    function number2alpha($number = 0, $suffix = null)
    {
        for ($alpha = ''; $number >= 0; $number = intval($number / 26) - 1) {
            $alpha = chr($number % 26 + 0x41) . $alpha;
        }

        return $alpha . $suffix;
    }
}

if (! function_exists('alpha2number')) {
    /*
     * Convert a string of uppercase letters to an integer.
     */
    function alpha2number($alpa = null, $suffix = null)
    {
        $length = strlen($alpha);
        $number = 0;

        for ($i = 0; $i < $l; $i++) {
            $number = $number * 26 + ord($alpha[$i]) - 0x40;
        }

        return ($number - 1) . $suffix;
    }
}

if (! function_exists('encrypt')) {
    /*
     * Encryption
     */
    function encrypt($passphrase = null)
    {
        if (! $passphrase) {
            return false;
        }

        $encrypter = \Config\Services::encrypter();

        return base64_encode($encrypter->encrypt($passphrase));
    }
}

if (! function_exists('decrypt')) {
    /*
     * Decryption
     */
    function decrypt($source = null)
    {
        if (! $source) {
            return false;
        }

        $encrypter = \Config\Services::encrypter();

        return $encrypter->decrypt(base64_decode($source));
    }
}
