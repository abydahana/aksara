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

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Config\Services;
use CodeIgniter\Session\Session;

class CommonHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Load the helper file before running tests
        helper('main'); // The file is mapped as 'main' helper in Autoload.php usually, checking...
        // Wait, aksara/Common.php is NOT a standard CI4 helper.
        // It's likely loaded by composer or index.php.
        // Let's ensure it's loaded.
        if (! function_exists('aksara')) {
            require_once APPPATH . 'Common.php';
        }
    }

    public function testAksaraVersion()
    {
        $version = aksara('version');
        $this->assertNotEmpty($version);
        $this->assertMatchesRegularExpression('/^\d+\.\d+\.\d+$/', $version, 'Version should be in X.Y.Z format');
    }

    public function testAksaraBuildVersion()
    {
        $buildVersion = aksara('build_version');
        $this->assertNotEmpty($buildVersion);
        $this->assertStringContainsString(aksara('version'), $buildVersion);
    }

    public function testAksaraUnknownParam()
    {
        $this->assertEquals('', aksara('unknown_param'));
    }

    public function testSetAndGetUserDataSession()
    {
        // Use anonymous class extending FakeSession
        $mockSession = new class () extends \Tests\Support\FakeSession {
            public $sessionData = [];
            public function set($key, $val = null): void
            {
                if (is_array($key)) {
                    foreach ($key as $k => $v) {
                        $this->sessionData[$k] = $v;
                    }
                } else {
                    $this->sessionData[$key] = $val;
                }
            }
            public function get($key = null)
            {
                if (null === $key) {
                    return $this->sessionData;
                }
                return $this->sessionData[$key] ?? null;
            }
            public function remove($key): void
            {
                unset($this->sessionData[$key]);
            }
        };

        // Inject the mock
        Services::injectMock('session', $mockSession);

        // Test set_userdata
        set_userdata('test_key', 'test_value');
        $this->assertEquals('test_value', get_userdata('test_key'));

        // Test unset_userdata
        unset_userdata('test_key');
        $this->assertNull(get_userdata('test_key'));
    }

    public function testIsRtl()
    {
        // Use anonymous class extending FakeSession
        $mockSession = new class () extends \Tests\Support\FakeSession {
            public $sessionData = [];
            public function get($key = null)
            {
                return $this->sessionData[$key] ?? null;
            }
            public function set($key, $val = null): void
            {
                $this->sessionData[$key] = $val;
            }
        };

        Services::injectMock('session', $mockSession);

        // Test Non-RTL
        $mockSession->set('language', 'en');
        $this->assertFalse(is_rtl());

        // Test RTL
        $mockSession->set('language', 'ar');
        $this->assertTrue(is_rtl());
    }
}
