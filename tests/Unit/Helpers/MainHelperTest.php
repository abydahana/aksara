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

class FakeSession
{
    public function get($key = null)
    {
        if ('user_id' === $key) {
            return null;
        }
        if ('session_generated' === $key) {
            return 'mock_session_id';
        }
        return null;
    }
    public function getFlashdata($key = null)
    {
        return [];
    }
    public function setFlashdata($key, $value)
    {
    }
}

class MainHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (! function_exists('array_sort')) {
            require_once APPPATH . 'Helpers/main_helper.php';
        }

        Services::injectMock('session', new FakeSession());
    }

    public function testArraySort()
    {
        $data = [
            ['name' => 'Charlie', 'age' => 30],
            ['name' => 'Alice', 'age' => 25],
            ['name' => 'Bob', 'age' => 25],
        ];

        // Sort by name ASC
        $sorted = array_sort($data, 'name', 'asc');
        $this->assertEquals('Alice', $sorted[0]['name']);
        $this->assertEquals('Bob', $sorted[1]['name']);
        $this->assertEquals('Charlie', $sorted[2]['name']);

        // Sort by age ASC
        $sortedAge = array_sort($data, 'age', 'asc');
        $this->assertEquals('Alice', $sortedAge[0]['name']); // Alice 25
        $this->assertEquals('Bob', $sortedAge[1]['name']); // Bob 25
    }

    public function testResetSort()
    {
        $data = [
            10 => 'a',
            20 => 'b',
            30 => 'c'
        ];
        $reset = reset_sort($data);
        $this->assertEquals([0, 1, 2], array_keys($reset));
        $this->assertEquals(['a', 'b', 'c'], $reset);

        $associative = [
            'first' => 'a',
            'second' => 'b'
        ];
        $this->assertEquals($associative, reset_sort($associative));
    }

    public function testGenerateToken()
    {
        if (! defined('ENCRYPTION_KEY')) {
            define('ENCRYPTION_KEY', 'test_key');
        }

        $token = generate_token('test/path', ['foo' => 'bar']);
        $this->assertNotEmpty($token);
        $this->assertEquals(12, strlen($token));
    }
}
