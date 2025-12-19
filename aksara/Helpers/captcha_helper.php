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

use GdImage;
use Throwable;

if (! function_exists('create_captcha')) {
    /**
     * Create CAPTCHA
     *
     * @param array|string $data      Data for the CAPTCHA or word
     * @param string       $img_path  Path to create the image in
     * @param string       $img_url   URL to the CAPTCHA image folder
     * @param string       $font_path Server path to font
     */
    function create_captcha(array|string $data = [], string $img_path = '', string $img_url = '', string $font_path = ''): array|bool
    {
        $defaults = [
            'word' => '',
            'img_path' => $img_path,
            'img_url' => $img_url,
            'img_width' => 150,
            'img_height' => 35,
            'font_path' => $font_path,
            'expiration' => 7200,
            'word_length' => 6,
            'font_size' => 16,
            'img_id' => 'captcha-' . uniqid(),
            'pool' => '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ',
            'colors' => [
                'background' => [255, 255, 255],
                'border' => [200, 200, 200],
                'text' => [50, 50, 50],
                'grid' => [235, 235, 235]
            ]
        ];

        // Merge configuration
        $config = is_array($data) ? array_merge($defaults, $data) : $defaults;

        // Check for GD extension and basic requirements
        if (! extension_loaded('gd') || empty($config['img_path']) || ! is_dir($config['img_path']) || ! is_writable($config['img_path'])) {
            return false;
        }

        // -----------------------------------
        // 1. Remove old images (Cleanup)
        // -----------------------------------
        $now = microtime(true);
        if ($handle = @opendir($config['img_path'])) {
            while (false !== ($filename = readdir($handle))) {
                if (preg_match('/^(\d+\.\d+)\.png$/', $filename, $matches)) {
                    if (($matches[1] + $config['expiration']) < $now) {
                        @unlink($config['img_path'] . $filename);
                    }
                }
            }
            closedir($handle);
        }

        // -----------------------------------
        // 2. Generate Word
        // -----------------------------------
        $word = $config['word'];
        if (empty($word)) {
            $word = '';
            $pool_length = strlen($config['pool']);
            for ($i = 0; $i < $config['word_length']; $i++) {
                try {
                    $word .= $config['pool'][random_int(0, $pool_length - 1)];
                } catch (Throwable $e) {
                    $word .= $config['pool'][mt_rand(0, $pool_length - 1)];
                }
            }
        }

        // -----------------------------------
        // 3. Image Creation (With Error Trapping)
        // -----------------------------------
        try {
            $im = imagecreatetruecolor($config['img_width'], $config['img_height']);

            // Validate that $im is actually a GdImage object (solves Intelephense P1007)
            if (! $im instanceof GdImage) {
                return false;
            }

            // Assign colors
            $colors = [];
            foreach ($config['colors'] as $key => $rgb) {
                $colors[$key] = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
            }

            // Create background
            imagefilledrectangle($im, 0, 0, $config['img_width'], $config['img_height'], $colors['background']);

            // -----------------------------------
            // 4. Distortions (Grid & Noise)
            // -----------------------------------
            // Draw subtle grid
            for ($i = 0; $i < $config['img_width']; $i += 15) {
                imageline($im, $i, 0, $i, $config['img_height'], $colors['grid']);
            }
            for ($i = 0; $i < $config['img_height']; $i += 15) {
                imageline($im, 0, $i, $config['img_width'], $i, $colors['grid']);
            }

            // Add random noise pixels
            for ($i = 0; $i < 60; $i++) {
                imagesetpixel($im, mt_rand(0, $config['img_width']), mt_rand(0, $config['img_height']), $colors['text']);
            }

            // -----------------------------------
            // 5. Write Text
            // -----------------------------------
            $use_font = (! empty($config['font_path']) && file_exists($config['font_path']));
            $x = 12;
            $length = strlen($word);

            for ($i = 0; $i < $length; $i++) {
                if ($use_font) {
                    $angle = mt_rand(-15, 15);
                    $y = mt_rand((int) ($config['img_height'] / 1.5), $config['img_height'] - 5);
                    imagettftext($im, $config['font_size'], $angle, (int) $x, (int) $y, $colors['text'], $config['font_path'], $word[$i]);
                } else {
                    $y = mt_rand(2, (int) ($config['img_height'] / 4));
                    imagestring($im, 5, (int) $x, (int) $y, $word[$i], $colors['text']);
                }
                $x += ($config['img_width'] - 20) / $length;
            }

            // Add Border
            imagerectangle($im, 0, 0, $config['img_width'] - 1, $config['img_height'] - 1, $colors['border']);

            // -----------------------------------
            // 6. Output & Cleanup
            // -----------------------------------
            $img_filename = $now . '.png';
            $img_url = rtrim($config['img_url'], '/') . '/';

            // Generate PNG file
            if (! imagepng($im, $config['img_path'] . $img_filename)) {
                return false;
            }

            // PHP 8+ Garbage Collection takes care of the GdImage object.
            // Setting to null is the modern way to explicitly free it.
            $im = null;

            return [
                'word' => $word,
                'time' => $now,
                'image' => '<img id="' . $config['img_id'] . '" src="' . $img_url . $img_filename . '" style="width: ' . $config['img_width'] . 'px; height: ' . $config['img_height'] . 'px; border: 0;" alt="CAPTCHA" />',
                'filename' => $img_filename
            ];
        } catch (Throwable $e) {
            // Gracefully fail if something goes wrong during image processing
            return false;
        }
    }
}
