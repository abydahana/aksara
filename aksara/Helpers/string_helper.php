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
     * @param   int $limit
     * @param   string $pad
     */
    function truncate($string = null, $limit = 0, $pad = '...')
    {
        if (! $string) {
            return false;
        }

        $string = str_ireplace(['<?php', '?>'], ['&lt;?php', '?&gt;'], $string);
        $string = str_ireplace(['<html', '</html>'], ['&lt;html', '&lt;/html&gt;'], $string);
        $string = str_ireplace(['<body', '</body>'], ['&lt;body', '&lt;/body&gt;'], $string);
        $string = str_ireplace(['<script', '</script>'], ['&lt;script', '&lt;/script&gt;'], $string);
        $string = str_ireplace(['<noscript', '</noscript>'], ['&lt;noscript', '&lt;/noscript&gt;'], $string);
        $string = str_ireplace(['<style', '</style>'], ['&lt;style', '&lt;/style&gt;'], $string);
        $string = str_ireplace(['<meta', '<link'], ['&lt;meta', '&lt;link'], $string);
        $string = str_ireplace(['<iframe', '</iframe>'], ['&lt;iframe', '&lt;/iframe&gt;'], $string);
        $string = str_ireplace(['<embed', '</embed>'], ['&lt;embed', '&lt;/embed&gt;'], $string);
        $string = str_ireplace(['<object', '</object>'], ['&lt;object', '&lt;/object&gt;'], $string);
        $string = strip_tags($string);
        $string = str_replace('&nbsp;', ' ', $string);
        $string = htmlspecialchars(str_replace(["\r", "\n"], '', strip_tags($string)));

        if ($limit && strlen($string) >= $limit) {
            $string = substr($string, 0, $limit) . $pad;
        }

        return $string;
    }
}

if (! function_exists('is_json')) {
    /**
     * Check if JSON is valid
     *
     * @params        string        $string
     *
     * @param mixed|null $string
     */
    function is_json($string = null)
    {
        if ($string && is_string($string)) {
            $string = json_decode($string, true);

            if (json_last_error() == JSON_ERROR_NONE) {
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

        if ($filename) {
            header('Content-disposition: attachment; filename=' . $filename . (stripos($filename, '.json') === false ? '.json' : null));
        }

        header('Content-Type: application/json');

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
            //$data = mb_convert_encoding($data, 'HTML-ENTITIES', mb_detect_encoding($data));
            $data = mb_encode_numericentity(
                htmlspecialchars_decode(
                    htmlentities($data, ENT_NOQUOTES, 'UTF-8', false),
                    ENT_NOQUOTES
                ),
                [0x80, 0x10FFFF, 0, ~0],
                'UTF-8'
            );
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
    function time_ago($datetime = null, bool $full = true, bool $short = false)
    {
        $time_difference = time() - strtotime($datetime);

        if ($time_difference < 1) {
            return strtolower(phrase('Just now'));
        }

        $condition = [
            (12 * 30 * 24 * 60 * 60) => ($full ? strtolower(phrase('year')) : strtolower(phrase('yr'))),
            (30 * 24 * 60 * 60) => strtolower(phrase('month')),
            (24 * 60 * 60) => strtolower(phrase('day')),
            (60 * 60) => ($full ? strtolower(phrase('hour')) : strtolower(phrase('hr'))),
            60 => ($full ? strtolower(phrase('minute')) : strtolower(phrase('min'))),
            1 => ($full ? strtolower(phrase('second')) : strtolower(phrase('sec')))
        ];

        foreach ($condition as $seconds => $label) {
            $day = $time_difference / $seconds;

            if ($day >= 1) {
                $time = round($day);
                return $time . ' ' . $label . ($time > 1 ? strtolower(phrase('s')) : '') . ' ' . (! $short ? strtolower(phrase('ago')) : null);
            }
        }
    }
}
