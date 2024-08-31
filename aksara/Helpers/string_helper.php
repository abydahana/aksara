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
            return;
        }

        $string = strip_tags($string);

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

if (! function_exists('related_generator')) {
    /**
     * Table of content generator
     *
     * @param   string $content
     */
    function related_generator($content = null, $related = [], int $per_paragraph = 5)
    {
        // Reformat related object into array
        $related = json_decode(json_encode($related), true);

        // Split the text into paragraphs
        $paragraphs = explode('</p>', $content);
        $updatedContent = '';
        $applied = false;

        if (sizeof($paragraphs) < $per_paragraph) {
            // Paragraph is lower than minimum, change default minimum setting
            $per_paragraph = sizeof($paragraphs);
        }

        foreach ($paragraphs as $index => $paragraph) {
            // If the paragraph is not empty, add the closing </p> tag
            if (! empty(trim($paragraph))) {
                $paragraph .= "</p>";
            }

            // Add the paragraph to the updated text
            $updatedContent .= $paragraph;

            // Add additional content after every 5th paragraph
            if (0 == ($index + 1) % $per_paragraph && ! empty(trim($paragraph)) && isset($related[($index / $per_paragraph)])) {
                $applied = true;
                $updatedContent .= '<div class="alert alert-info callout"><p class="mb-0">' . phrase('Peoples also read') . '</p><a href="' . $related[($index / $per_paragraph)]['link'] . '" class="--xhr">' . $related[($index / $per_paragraph)]['title'] . '</a></div>';
            }
        }

        if (! $applied && $related) {
            $updatedContent .= '<div class="alert alert-info callout"><p class="mb-0">' . phrase('Peoples also read') . '</p><a href="' . $related[0]['link'] . '" class="--xhr">' . $related[0]['title'] . '</a></div>';
        }

        return $updatedContent;
    }
}

if (! function_exists('toc_generator')) {
    /**
     * Table of content generator
     *
     * @param   string $content
     */
    function toc_generator($content = null, $related = [])
    {
        $toc = null; // Start the table of contents
        $pattern = '/<h([1-6])[^>]*>(.*?)<\/h\1>/i'; // Regex pattern to find headings (h1 to h6)
        $matches = [];

        // Find all headings in the content
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $key => $match) {
            $level = $match[1]; // Heading level (e.g., 1 for h1, 2 for h2)
            $title = $match[2]; // The text inside the heading
            $slug = format_slug($title); // Create a URL-friendly ID

            // Add ID attribute to the heading in the content
            $content = str_replace($match[0], "<h$level id=\"$slug\">$title</h$level>", $content);

            // Add a list item to the TOC
            $toc .= "<li class=\"toc-level-$level\"><a href=\"#$slug\" class=\"lead\">$title</a></li>";
        }

        if ($toc) {
            $toc = '<ul class="mb-0">' . $toc . '</ul>';
        }

        return [$toc, $content];
    }
}

if (! function_exists('fetch_metadata')) {
    /**
     * Fetching metadata from url path
     */
    function fetch_metadata(string $path)
    {
        try {
            $client = service('curlrequest');

            $response = $client->request('GET', base_url($path), [
                'headers' => [
                    'X-Requested-With' => 'XMLHttpRequest',
                    'X-API-KEY' => ENCRYPTION_KEY
                ],
                'query' => [
                    '__fetch_metadata' => true
                ]
            ]);

            return json_decode($response->getBody());
        } catch (\Throwable $e) {
            return $e;
        }

        return [];
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
