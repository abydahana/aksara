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
use Config\Services;

class FakeSiteURI
{
    protected $baseURL;
    public function __construct($baseURL)
    {
        $this->baseURL = $baseURL;
    }
    public function baseUrl($path = '')
    {
        return $this->baseURL . $path;
    }
}

class FakeRequest
{
    protected $uri;
    public function __construct($uri)
    {
        $this->uri = $uri;
    }
    public function getUri()
    {
        return $this->uri;
    }
    public function getGet($key = null)
    {
        return [];
    }
    public function getServer($key = null)
    {
        return null;
    }
    public function isAJAX()
    {
        return false;
    }
}

class UrlHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (! function_exists('base_url')) {
            require_once APPPATH . 'Helpers/url_helper.php';
        }
        if (! function_exists('generate_token')) {
            require_once APPPATH . 'Helpers/main_helper.php';
        }

        // Mock Session too because generate_token (used in base_url) uses it
        // We can use the same Fake logic or a simpler one since base_url tests here don't trigger generate_token heavily
        // Actually base_url calls generate_token
        // So we need to mock session for get_userdata('session_generated')

        $mockSession = new \stdClass(); // Or FakeSession if we need specific return
        // Ideally we should reuse FakeSession from MainHelperTest if it was shared.
        // For now, let's inject a simple fake that returns null/empty string implicitly for unknown methods?
        // But stdClass doesn't handle method calls.
        // Let's use anonymous class or simple fake

        // base_url calls generate_token calls get_userdata calls service('session')->get(...)
        $mockSession = new class () {
            public function get($key = null)
            {
                return 'mock_session';
            }
            public function getFlashdata($key = null)
            {
                return [];
            }
        };
        Services::injectMock('session', $mockSession);
    }

    public function testBaseUrlBasic()
    {
        // Mock the Config\App
        $config = new \Config\App();
        $config->baseURL = 'http://localhost:8080/';
        $config->indexPage = '';

        // Inject config
        \CodeIgniter\Config\Factories::injectMock('config', 'App', $config);

        // Mock Request and SiteURI using Fakes
        $fakeUri = new FakeSiteURI($config->baseURL);
        $fakeRequest = new FakeRequest($fakeUri);

        Services::injectMock('request', $fakeRequest);

        $this->assertEquals('http://localhost:8080/foo', base_url('foo'));
    }

    public function testAssetUrl()
    {
        $config = new \Config\App();
        $config->baseURL = 'http://localhost:8080/';
        $config->indexPage = '';
        \CodeIgniter\Config\Factories::injectMock('config', 'App', $config);

        $fakeUri = new FakeSiteURI($config->baseURL);
        $fakeRequest = new FakeRequest($fakeUri);

        Services::injectMock('request', $fakeRequest);

        $this->assertEquals('http://localhost:8080/assets/img.jpg', asset_url('img.jpg'));
    }
}
