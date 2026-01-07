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

class StringHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (! function_exists('truncate')) {
            require_once APPPATH . 'Helpers/string_helper.php';
        }
        if (! function_exists('phrase')) {
            require_once APPPATH . 'Common.php';
        }
    }

    public function testTruncate()
    {
        $this->assertEquals('Hello...', truncate('Hello World', 5));
        $this->assertEquals('Hello World', truncate('Hello World', 20));
        $this->assertEquals('', truncate(''));
        $this->assertEquals('He...', truncate('Hello World', 2, '...'));
    }

    public function testCustomNl2br()
    {
        // Simple test to ensure it runs and returns string
        $result = custom_nl2br("Line 1\nLine 2");
        $this->assertIsString($result);
        $this->assertStringContainsString('Line 1', $result);
    }

    public function testIsJson()
    {
        $this->assertTrue(is_json('{"key": "value"}'));
        $this->assertTrue(is_json('[1, 2, 3]'));
        $this->assertFalse(is_json('Invalid JSON'));
        $this->assertFalse(is_json(''));
    }

    public function testEncodingFixer()
    {
        // Simple pass-through test for standard strings
        $this->assertEquals('test', encoding_fixer('test'));
        $array = ['key' => 'value'];
        $this->assertEquals($array, encoding_fixer($array));
    }

    public function testFormatSlug()
    {
        $this->assertEquals('hello-world', format_slug('Hello World'));
        $this->assertEquals('hello-world', format_slug('Hello  World'));
        $this->assertEquals('hello-world-123', format_slug('Hello World 123'));
        $this->assertEquals('a-b-c', format_slug('a_b_c'));
    }

    public function testValidHex()
    {
        $this->assertTrue(valid_hex('#FFF'));
        $this->assertTrue(valid_hex('#ffffff'));
        $this->assertTrue(valid_hex('#000000'));
        $this->assertFalse(valid_hex('FFF'));
        $this->assertFalse(valid_hex('#GGGGGG'));
        $this->assertFalse(valid_hex('red'));
    }

    public function testNumber2Alpha()
    {
        $this->assertEquals('A', number2alpha(0));
        $this->assertEquals('B', number2alpha(1));
        $this->assertEquals('Z', number2alpha(25));
        $this->assertEquals('AA', number2alpha(26));
    }

    public function testAlpha2Number()
    {
        $this->assertEquals('0', alpha2number('A'));
        $this->assertEquals('1', alpha2number('B'));
        $this->assertEquals('25', alpha2number('Z'));
        $this->assertEquals('26', alpha2number('AA'));
    }
}
