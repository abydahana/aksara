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

/**
 * CodeIgniter CAPTCHA Helper
 *
 * @category    Helpers
 * @author      EllisLab Dev Team
 * @see         https://codeigniter.com/user_guide/helpers/captcha_helper.html
 */

// ------------------------------------------------------------------------

if (! function_exists('create_captcha')) {
    /**
     * Create CAPTCHA
     *
     * @param array  $data      Data for the CAPTCHA
     * @param string $img_path  Path to create the image in (deprecated)
     * @param string $img_url   URL to the CAPTCHA image folder (deprecated)
     * @param string $font_path Server path to font (deprecated)
     *
     * @return string
     */
    function create_captcha($data = '', $img_path = '', $img_url = '', $font_path = '')
    {
        $defaults = [
            'word' => '',
            'img_path' => '',
            'img_url' => '',
            'img_width' => '150',
            'img_height' => '30',
            'font_path' => '',
            'expiration' => 7200,
            'word_length' => 8,
            'font_size' => 16,
            'img_id' => '',
            'pool' => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'colors' => [
                'background' => [255, 255, 255],
                'border' => [153, 102, 102],
                'text' => [204, 153, 153],
                'grid' => [255, 182, 182]
            ]
        ];

        foreach ($defaults as $key => $val) {
            if (! is_array($data) && empty($$key)) {
                $$key = $val;
            } else {
                $$key = isset($data[$key]) ? $data[$key] : $val;
            }
        }

        if ('' === $img_path or '' === $img_url
                             or ! is_dir($img_path) or ! is_really_writable($img_path)
                             or ! extension_loaded('gd')) {
            return false;
        }

        // -----------------------------------
        // Remove old images
        // -----------------------------------

        $now = microtime(true);

        try {
            $current_dir = @opendir($img_path);
            while ($filename = @readdir($current_dir)) {
                if (in_array(substr($filename, -4), ['.jpg', '.png'])
                    && (str_replace(['.jpg', '.png'], '', $filename) + $expiration) < $now) {
                    @unlink($img_path.$filename);
                }
            }

            @closedir($current_dir);
        } catch(\Throwable $e) {
        }

        if (! is_string($word)) {
            $word = (string) $word;
        } elseif (empty($word)) {
            $word = substr(str_shuffle(str_repeat($pool, ceil($word_length / strlen($pool)))), 1, $word_length);
        }

        // -----------------------------------
        // Determine angle and position
        // -----------------------------------
        $length = strlen($word);
        $angle = ($length >= 6) ? mt_rand(-($length - 6), ($length - 6)) : 0;
        $x_axis = mt_rand(6, (360 / $length) - 16);
        $y_axis = ($angle >= 0) ? mt_rand($img_height, $img_width) : mt_rand(6, $img_height);

        // Create image
        // PHP.net recommends imagecreatetruecolor(), but it isn't always available
        $im = function_exists('imagecreatetruecolor')
            ? imagecreatetruecolor($img_width, $img_height)
            : imagecreate($img_width, $img_height);

        // -----------------------------------
        //  Assign colors
        // ----------------------------------

        is_array($colors) or $colors = $defaults['colors'];

        foreach (array_keys($defaults['colors']) as $key) {
            // Check for a possible missing value
            is_array($colors[$key]) or $colors[$key] = $defaults['colors'][$key];
            $colors[$key] = imagecolorallocate($im, $colors[$key][0], $colors[$key][1], $colors[$key][2]);
        }

        // Create the rectangle
        ImageFilledRectangle($im, 0, 0, $img_width, $img_height, $colors['background']);

        // -----------------------------------
        //  Create the spiral pattern
        // -----------------------------------
        $theta = 1;
        $thetac = 7;
        $radius = 16;
        $circles = 20;
        $points = 32;

        for ($i = 0, $cp = ($circles * $points) - 1; $i < $cp; $i++) {
            $theta += $thetac;
            $rad = $radius * ($i / $points);
            $x = (int) ($rad * cos($theta)) + $x_axis;
            $y = (int) ($rad * sin($theta)) + $y_axis;
            $theta += $thetac;
            $rad1 = $radius * (($i + 1) / $points);
            $x1 = (int) ($rad1 * cos($theta)) + $x_axis;
            $y1 = (int) ($rad1 * sin($theta)) + $y_axis;
            imageline($im, $x, $y, $x1, $y1, $colors['grid']);
            $theta -= $thetac;
        }

        // -----------------------------------
        //  Write the text
        // -----------------------------------

        $use_font = ('' !== $font_path && file_exists($font_path) && function_exists('imagettftext'));
        if (false === $use_font) {
            ($font_size > 5) && $font_size = 5;
            $x = mt_rand(0, $img_width / ($length / 3));
            $y = 0;
        } else {
            ($font_size > 30) && $font_size = 30;
            $x = mt_rand(0, $img_width / ($length / 1.5));
            $y = $font_size + 2;
        }

        for ($i = 0; $i < $length; $i++) {
            if (false === $use_font) {
                $y = mt_rand(0, $img_height / 2);
                imagestring($im, $font_size, $x, $y, $word[$i], $colors['text']);
                $x += ($font_size * 2);
            } else {
                $y = mt_rand($img_height / 2, $img_height - 3);
                imagettftext($im, $font_size, $angle, $x, $y, $colors['text'], $font_path, $word[$i]);
                $x += $font_size;
            }
        }

        // Create the border
        imagerectangle($im, 0, 0, $img_width - 1, $img_height - 1, $colors['border']);

        // -----------------------------------
        //  Generate the image
        // -----------------------------------
        $img_url = rtrim($img_url, '/').'/';

        if (function_exists('imagejpeg')) {
            $img_filename = $now.'.jpg';
            imagejpeg($im, $img_path.$img_filename);
        } elseif (function_exists('imagepng')) {
            $img_filename = $now.'.png';
            imagepng($im, $img_path.$img_filename);
        } else {
            return false;
        }

        $img = '<img '.('' === $img_id ? '' : 'id="'.$img_id.'"').' src="'.$img_url.$img_filename.'" style="width: '.$img_width.'px; height: '.$img_height .'px; border: 0;" alt=" " />';
        ImageDestroy($im);

        return ['word' => $word, 'time' => $now, 'image' => $img, 'filename' => $img_filename];
    }
}
