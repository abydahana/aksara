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

namespace Tests\Unit\Helpers;

use CodeIgniter\Test\CIUnitTestCase;

class CaptchaHelperTest extends CIUnitTestCase
{
    protected $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        if (! function_exists('create_captcha')) {
            require_once APPPATH . 'Helpers/captcha_helper.php';
        }

        $this->tempDir = sys_get_temp_dir() . '/aksara_test_captcha/';
        if (! is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        // Cleanup temp files safely in system temp dir
        $files = glob($this->tempDir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        if (is_dir($this->tempDir)) {
            rmdir($this->tempDir);
        }
        parent::tearDown();
    }

    public function testCreateCaptchaBasic()
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('GD extension is not loaded.');
        }

        $result = create_captcha([
            'img_path' => $this->tempDir,
            'img_url' => 'http://localhost/captcha',
            'font_path' => '' // Use system font/defaults
        ]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('word', $result);
        $this->assertArrayHasKey('image', $result);
        $this->assertFileExists($this->tempDir . $result['filename']);

        // Check image content type logic (implied by success)
        $this->assertStringContainsString('<img', $result['image']);
    }
}
