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

class FakeSiteURIWidget
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

class FakeRequestWidget
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

class FakeSessionWidget
{
    public function get($key = null)
    {
        return 'mock_session';
    }
    public function getFlashdata($key = null)
    {
        return [];
    }
}

class WidgetHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (! function_exists('comment_widget')) {
            require_once APPPATH . 'Helpers/widget_helper.php';
        }
        if (! function_exists('base_url')) {
            require_once APPPATH . 'Helpers/url_helper.php';
        }
        if (! function_exists('generate_token')) {
            require_once APPPATH . 'Helpers/main_helper.php';
        }

        Services::injectMock('session', new FakeSessionWidget());
    }

    public function testCommentWidget()
    {
        // Mock Request/Config for base_url
        $config = new \Config\App();
        $config->baseURL = 'http://localhost/';
        $config->indexPage = '';
        \CodeIgniter\Config\Factories::injectMock('config', 'App', $config);

        $fakeUri = new FakeSiteURIWidget($config->baseURL);
        $fakeRequest = new FakeRequestWidget($fakeUri);

        Services::injectMock('request', $fakeRequest);

        $output = comment_widget(['id' => 1]);

        $this->assertStringContainsString('data-source="http://localhost/xhr/widget/comment', $output);
    }
}
