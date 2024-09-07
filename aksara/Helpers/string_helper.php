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
     * @param   int $limit
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

        $html = null;

        if (isset($data->html)) {
            $html = $data->html;

            /* make a backup of "pre" tag */
            preg_match_all('#\<pre.*\>(.*)\<\/pre\>#Uis', $html, $pre_backup);
            $html = str_replace($pre_backup[0], array_map(function ($element) {return '<pre>' . $element . '</pre>';}, array_keys($pre_backup[0])), $html);

            $html = trim(preg_replace(['/\s+/', '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\>)\s*(\<)/m'], [' ', '>', '<', '$1$2'], $html));

            /* rollback the pre tag */
            $html = str_replace(array_map(function ($element) {return '<pre>' . $element . '</pre>';}, array_keys($pre_backup[0])), $pre_backup[0], $html);
        }

        if ($html) {
            $data->html = $html;
        }

        if (isset($data->status) && 200 === $data->status) {
            $data->_token = sha1(current_page() . ENCRYPTION_KEY . get_userdata('session_generated'));
        }

        $data = encoding_fixer($data);

        $minify_pattern = [
            '/[\r\n\t\s]+/' => ' ',     // Replace end of line by space
            '/\>[^\S ]+/s' => '>',      // Strip whitespaces after tags, except space
            '/[^\S ]+\</s' => '<',      // Strip whitespaces before tags, except space
            '/(\s)+/s' => '\\1',        // Shorten multiple whitespace sequences
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

        $condition = [
            (12 * 30 * 24 * 60 * 60) => ($full ? phrase('year') : phrase('yr')),
            (30 * 24 * 60 * 60) => ($full ? phrase('month') : phrase('mo')),
            (7 * 24 * 60 * 60) => phrase('week'),
            (24 * 60 * 60) => phrase('day'),
            (60 * 60) => ($full ? phrase('hour') : phrase('hr')),
            60 => ($full ? phrase('minute') : phrase('min')),
            1 => ($full ? phrase('second') : phrase('sec'))
        ];

        foreach ($condition as $seconds => $label) {
            $time_period = $time_difference / $seconds;

            if ($time_period >= 1) {
                $time = round($time_period);

                if ($full && 1 == $time && phrase('day') == $label) {
                    $time = null;
                    $label = phrase('Yesterday');
                }

                return $time . ' ' . $label . ($time > 1 ? phrase('s') : null) . (! $short && $time ? ' ' . phrase('ago') : null);
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
