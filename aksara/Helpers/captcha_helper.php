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

if (! function_exists('create_captcha')) {
    /**
     * Create CAPTCHA
     */
    function create_captcha(array|string $data = [], string $img_path = '', string $imgUrl = '', string $fontPath = ''): array|bool
    {
        $defaults = [
            'word' => '',
            'img_path' => $img_path,
            'img_url' => $imgUrl,
            'font_path' => $fontPath,
            'img_width' => 150,
            'img_height' => 35,
            'font_size' => 16,
            'expiration' => 7200,
            'word_length' => 6,
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
        // 1. Generate Word
        // -----------------------------------
        $word = $config['word'];
        if (empty($word)) {
            $word = '';
            $poolLength = strlen($config['pool']);
            for ($i = 0; $i < $config['word_length']; $i++) {
                try {
                    $word .= $config['pool'][random_int(0, $poolLength - 1)];
                } catch (\Throwable $e) {
                    $word .= $config['pool'][mt_rand(0, $poolLength - 1)];
                }
            }
        }

        // -----------------------------------
        // 2. Image Creation (With Distortion)
        // -----------------------------------
        try {
            $now = microtime(true);
            $width = (int) $config['img_width'];
            $height = (int) $config['img_height'];

            // Temporary image for text before distortion
            $imTmp = imagecreatetruecolor($width, $height);
            $im = imagecreatetruecolor($width, $height);

            if (! $imTmp instanceof \GdImage || ! $im instanceof \GdImage) {
                return false;
            }

            // Assign colors
            $colors = [];
            $colorsTmp = [];
            foreach ($config['colors'] as $key => $rgb) {
                $colors[$key] = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
                $colorsTmp[$key] = imagecolorallocate($imTmp, $rgb[0], $rgb[1], $rgb[2]);
            }

            // Background for both
            imagefilledrectangle($imTmp, 0, 0, $width, $height, $colorsTmp['background']);
            imagefilledrectangle($im, 0, 0, $width, $height, $colors['background']);

            // -----------------------------------
            // 3. Write Text to Temp Image
            // -----------------------------------
            $use_font = (! empty($config['font_path']) && file_exists($config['font_path']));
            $x = 10;
            $length = strlen($word);

            for ($i = 0; $i < $length; $i++) {
                // Random color for each character
                $char_color = imagecolorallocate($imTmp, mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150));

                if ('dark' == get_userdata('app_theme')) {
                    $char_color = $colorsTmp['text'];
                }

                if ($use_font) {
                    $angle = mt_rand(-15, 15);
                    $y = mt_rand((int) ($height / 1.4), $height - 5);
                    imagettftext($imTmp, (int) $config['font_size'], $angle, (int) $x, (int) $y, $char_color, $config['font_path'], $word[$i]);
                } else {
                    $y = mt_rand(2, (int) ($height / 4));
                    imagestring($imTmp, 5, (int) $x, (int) $y, $word[$i], $char_color);
                }
                $x += ($width - 20) / $length;
            }

            // -----------------------------------
            // 4. Apply Sinusoidal Distortion
            // -----------------------------------
            $freq = mt_rand(5, 8) / 100; // Frequency
            $amp = mt_rand(3, 5);         // Amplitude
            $phase = mt_rand(0, 10);      // Phase shift

            for ($i = 0; $i < $width; $i++) {
                $offset = (int) (sin($i * $freq + $phase) * $amp);
                imagecopy($im, $imTmp, $i, $offset, $i, 0, 1, $height);
            }

            // -----------------------------------
            // 5. Draw Noise (Foreground - Balanced)
            // -----------------------------------
            // Draw grid OVER the text (less dense)
            for ($i = 0; $i < $width; $i += 20) {
                imageline($im, $i, 0, $i, $height, $colors['grid']);
            }
            for ($i = 0; $i < $height; $i += 20) {
                imageline($im, 0, $i, $width, $i, $colors['grid']);
            }

            imagesetthickness($im, 1);

            // Lighter crossing lines (only 2)
            for ($i = 0; $i < 2; $i++) {
                $line_color = ('dark' == get_userdata('app_theme') ? imagecolorallocate($im, mt_rand(40, 70), mt_rand(40, 70), mt_rand(40, 70)) : imagecolorallocate($im, mt_rand(210, 235), mt_rand(210, 235), mt_rand(210, 235)));
                imageline($im, 0, mt_rand(0, $height), $width, mt_rand(0, $height), $line_color);
            }

            // Random arcs (only 2, lighter)
            for ($i = 0; $i < 2; $i++) {
                $arc_color = ('dark' == get_userdata('app_theme') ? imagecolorallocate($im, mt_rand(40, 70), mt_rand(40, 70), mt_rand(40, 70)) : imagecolorallocate($im, mt_rand(210, 235), mt_rand(210, 235), mt_rand(210, 235)));
                imagearc($im, mt_rand(0, $width), mt_rand(0, $height), mt_rand(50, 150), mt_rand(30, 100), mt_rand(0, 360), mt_rand(0, 360), $arc_color);
            }

            // Random noise pixels (back to 50)
            for ($i = 0; $i < 50; $i++) {
                imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $colors['text']);
            }

            // Add Border
            imagerectangle($im, 0, 0, $width - 1, $height - 1, $colors['border']);

            // -----------------------------------
            // 6. Output & Cleanup
            // -----------------------------------
            $imgFilename = $now . '.png';
            $imgUrl = rtrim($config['img_url'], '/') . '/';

            if (! imagepng($im, $config['img_path'] . $imgFilename)) {
                return false;
            }

            destroy_captcha_image($im);
            destroy_captcha_image($imTmp);

            return [
                'word' => $word,
                'time' => $now,
                'image' => '<img id="' . $config['img_id'] . '" src="' . $imgUrl . $imgFilename . '" style="width: ' . $width . 'px; height: ' . $height . 'px; border: 0;" alt="CAPTCHA" />',
                'filename' => $imgFilename
            ];
        } catch (\Throwable $e) {
            log_message('error', '[CAPTCHA] ' . $e->getMessage());

            return false;
        }
    }
}

if (! function_exists('destroy_captcha_image')) {
    /**
     * Release CAPTCHA image references without triggering PHP 8.5 imagedestroy deprecation.
     */
    function destroy_captcha_image(?GdImage &$image): void
    {
        unset($image);
    }
}

if (! function_exists('generate_captcha')) {
    /**
     * Generate CAPTCHA wrapper
     */
    function generate_captcha(): array
    {
        $string = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';
        $length = 6;
        $captcha = [];

        if (is_writable(UPLOAD_PATH)) {
            if (! is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha')) {
                try {
                    mkdir(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha', 0755, true);
                } catch (\Throwable $e) {
                    log_message('error', '[CAPTCHA] ' . $e->getMessage());
                }
            }

            if (is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha') && is_writable(UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha')) {
                // Try to use a smoother TTF font from vendor if available
                $fontPath = ROOTPATH . 'vendor' . DIRECTORY_SEPARATOR . 'mpdf' . DIRECTORY_SEPARATOR . 'mpdf' . DIRECTORY_SEPARATOR . 'ttfonts' . DIRECTORY_SEPARATOR . 'DejaVuSans.ttf';

                $colorScheme = ('dark' === get_userdata('app_theme') ? [
                    'background' => [15, 15, 15],
                    'border' => [15, 15, 15],
                    'grid' => [50, 50, 50],
                    'text' => [255, 255, 255]
                ] : [
                    'background' => [255, 255, 255],
                    'border' => [255, 255, 255],
                    'grid' => [200, 200, 200],
                    'text' => [0, 0, 0]
                ]);

                $captcha = create_captcha([
                    'img_path' => UPLOAD_PATH . DIRECTORY_SEPARATOR . 'captcha' . DIRECTORY_SEPARATOR,
                    'img_url' => base_url(UPLOAD_PATH . '/captcha'),
                    'img_width' => 120,
                    'img_height' => 35,
                    'font_path' => (file_exists($fontPath) ? $fontPath : ''),
                    'font_size' => 16,
                    'expiration' => 3600,
                    'word_length' => $length,
                    'pool' => $string,
                    'colors' => $colorScheme
                ]);
            }
        }

        if (! $captcha) {
            $captcha = [
                'word' => substr(str_shuffle(str_repeat($string, ceil($length / strlen($string)))), 1, $length),
                'filename' => null
            ];
        }

        // Set captcha word into session, used to next validation
        set_userdata([
            'captcha' => $captcha['word'],
            'captcha_file' => $captcha['filename']
        ]);

        return [
            'image' => ($captcha['filename'] ? base_url(UPLOAD_PATH . '/captcha/' . $captcha['filename']) : null),
            'string' => (! $captcha['filename'] ? $captcha['word'] : null)
        ];
    }
}
