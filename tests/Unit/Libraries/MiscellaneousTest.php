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

namespace Tests\Unit\Libraries;

use CodeIgniter\Test\CIUnitTestCase;
use Aksara\Libraries\Miscellaneous;
use Config\Services;
use Tests\Support\FakeSiteURI;
use Tests\Support\FakeRequest;

class MiscellaneousTest extends CIUnitTestCase
{
    protected $tempDir;

    protected function setUp(): void
    {
        parent::setUp();

        if (! function_exists('get_image')) {
            require_once APPPATH . 'Helpers/file_helper.php';
        }
        if (! function_exists('base_url')) {
            require_once APPPATH . 'Helpers/url_helper.php';
        }
        if (! function_exists('get_userdata')) {
            require_once APPPATH . 'Common.php';
        }

        $this->tempDir = sys_get_temp_dir() . '/aksara_test_misc/';
        if (! is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }
    }

    public function testBarcodeGenerator()
    {
        // Mock Config/Request for get_image -> base_url
        $config = new \Config\App();
        $config->baseURL = 'http://localhost/';
        $config->indexPage = '';
        \CodeIgniter\Config\Factories::injectMock('config', 'App', $config);

        $fakeUri = new FakeSiteURI($config->baseURL);
        $fakeRequest = new FakeRequest($fakeUri);

        Services::injectMock('request', $fakeRequest);

        $misc = new Miscellaneous();
        $param = 'TEST-' . uniqid();
        $result = $misc->barcode_generator($param);

        $this->assertNotEmpty($result);
        $this->assertStringContainsString('http://localhost/', $result);
        $this->assertStringContainsString('_barcode', $result);
    }

    public function testQrcodeGenerator()
    {
        $config = new \Config\App();
        $config->baseURL = 'http://localhost/';
        $config->indexPage = '';
        \CodeIgniter\Config\Factories::injectMock('config', 'App', $config);

        $fakeUri = new FakeSiteURI($config->baseURL);
        $fakeRequest = new FakeRequest($fakeUri);

        Services::injectMock('request', $fakeRequest);

        $misc = new Miscellaneous();
        $param = 'TEST-QR-' . uniqid();
        $result = $misc->qrcode_generator($param);

        $this->assertNotEmpty($result);
        $this->assertStringContainsString('_qrcode', $result);
    }
}
