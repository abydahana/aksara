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
use Tests\Support\FakeSiteURI;
use Tests\Support\FakeRequest;
use Tests\Support\FakeSession;

class ThemeHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (! function_exists('generate_menu')) {
            require_once APPPATH . 'Helpers/theme_helper.php';
        }
        if (! function_exists('base_url')) {
            require_once APPPATH . 'Helpers/url_helper.php';
        }
        if (! function_exists('generate_token')) {
            require_once APPPATH . 'Helpers/main_helper.php';
        }

        Services::injectMock('session', new FakeSession());
    }

    public function testGenerateMenuInternal()
    {
        // Mock URI service because generate_menu checks active state
        // We use FakeSiteURI which implements getSegments, getPath
        $fakeUri = new FakeSiteURI('http://localhost/');
        // Allow modifying segments for test?
        // FakeSiteURI hardcodes [] in getSegments.
        // We need a wrapper or dynamic fake.

        $fakeUri = new class ('http://localhost/') extends FakeSiteURI {
            public function getSegments()
            {
                return ['dashboard'];
            }
            public function getPath()
            {
                return 'dashboard';
            }
        };

        Services::injectMock('uri', $fakeUri);

        // Mock Request for base_url
        $config = new \Config\App();
        $config->baseURL = 'http://localhost/';
        $config->indexPage = '';
        \CodeIgniter\Config\Factories::injectMock('config', 'App', $config);

        // Use standard FakeSiteURI for Request (or the one above)
        $fakeRequest = new FakeRequest($fakeUri);
        Services::injectMock('request', $fakeRequest);


        $menus = [
            (object) [
                'id' => 1,
                'label' => 'Dashboard',
                'slug' => 'dashboard',
                'icon' => 'mdi mdi-view-dashboard',
                'class' => '',
                'new_tab' => 0,
                'children' => []
            ]
        ];

        $output = generate_menu($menus);

        $this->assertStringContainsString('Dashboard', $output);
        $this->assertStringContainsString('href="http://localhost/dashboard"', $output);
        $this->assertStringContainsString('active', $output);
    }

    public function testGenerateMenuExternal()
    {
        // Mock Services
        $fakeUri = new FakeSiteURI('http://localhost/');
        Services::injectMock('uri', $fakeUri);

        $config = new \Config\App();
        $config->baseURL = 'http://localhost/';
        $config->indexPage = '';
        \CodeIgniter\Config\Factories::injectMock('config', 'App', $config);

        $fakeRequest = new FakeRequest($fakeUri);
        Services::injectMock('request', $fakeRequest);

        $menus = [
            (object) [
                'id' => 2,
                'label' => 'Google',
                'slug' => 'https://google.com',
                'icon' => 'mdi mdi-google',
                'class' => '',
                'new_tab' => 1,
                'children' => []
            ]
        ];

        $output = generate_menu($menus);

        $this->assertStringContainsString('href="https://google.com"', $output);
        $this->assertStringContainsString('target="_blank"', $output);
    }
}
