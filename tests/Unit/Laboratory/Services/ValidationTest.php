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

namespace Tests\Unit\Laboratory\Services;

use PHPUnit\Framework\TestCase;
use Aksara\Laboratory\Services\Validation;

class ValidationTest extends TestCase
{
    private Validation $validation;

    protected function setUp(): void
    {
        $this->validation = new Validation();
    }

    /**
     * Test boolean validation with valid values
     */
    public function testBooleanWithValidValues()
    {
        $this->assertTrue($this->validation->boolean(null));
        $this->assertTrue($this->validation->boolean(0));
        $this->assertTrue($this->validation->boolean(1));
        $this->assertTrue($this->validation->boolean('0'));
        $this->assertTrue($this->validation->boolean('1'));
    }

    /**
     * Test boolean validation with invalid values
     */
    public function testBooleanWithInvalidValues()
    {
        $this->assertFalse($this->validation->boolean(2));
        $this->assertFalse($this->validation->boolean('yes'));
        $this->assertFalse($this->validation->boolean('no'));
        $this->assertFalse($this->validation->boolean(true));
        $this->assertFalse($this->validation->boolean(false));
    }

    /**
     * Test currency validation with valid formats
     */
    public function testCurrencyWithValidFormats()
    {
        $this->assertTrue($this->validation->currency('100'));
        $this->assertTrue($this->validation->currency('1000'));
        $this->assertTrue($this->validation->currency('1,000'));
        $this->assertTrue($this->validation->currency('1,000.00'));
        $this->assertTrue($this->validation->currency('$1,000.00'));
        $this->assertTrue($this->validation->currency('$ 1,000.00'));
        $this->assertTrue($this->validation->currency('  100.50  '));
        $this->assertTrue($this->validation->currency('999,999,999.99'));
    }

    /**
     * Test currency validation with invalid formats
     */
    public function testCurrencyWithInvalidFormats()
    {
        $this->assertFalse($this->validation->currency('abc'));
        $this->assertFalse($this->validation->currency('1,00'));
        $this->assertFalse($this->validation->currency('1000.0'));
        $this->assertFalse($this->validation->currency('1000.000'));
        $this->assertFalse($this->validation->currency('$1000,00'));
        $this->assertFalse($this->validation->currency(''));
    }

    /**
     * Test valid_year with valid years
     */
    public function testValidYearWithValidYears()
    {
        $this->assertTrue($this->validation->valid_year(1970));
        $this->assertTrue($this->validation->valid_year(2000));
        $this->assertTrue($this->validation->valid_year(2024));
        $this->assertTrue($this->validation->valid_year(2100));
        $this->assertTrue($this->validation->valid_year('2025'));
    }

    /**
     * Test valid_year with invalid years
     */
    public function testValidYearWithInvalidYears()
    {
        $this->assertFalse($this->validation->valid_year(1969));
        $this->assertFalse($this->validation->valid_year(2101));
        $this->assertFalse($this->validation->valid_year(1900));
        $this->assertFalse($this->validation->valid_year(3000));
        $this->assertFalse($this->validation->valid_year('abc'));
        $this->assertFalse($this->validation->valid_year(null));
    }

    /**
     * Test valid_hex with valid hex color codes
     */
    public function testValidHexWithValidCodes()
    {
        $this->assertTrue($this->validation->valid_hex('#fff'));
        $this->assertTrue($this->validation->valid_hex('#FFF'));
        $this->assertTrue($this->validation->valid_hex('#ffffff'));
        $this->assertTrue($this->validation->valid_hex('#FFFFFF'));
        $this->assertTrue($this->validation->valid_hex('#123abc'));
        $this->assertTrue($this->validation->valid_hex('#ABC123'));
        $this->assertTrue($this->validation->valid_hex('#000'));
        $this->assertTrue($this->validation->valid_hex('#000000'));
    }

    /**
     * Test valid_hex with invalid hex color codes
     */
    public function testValidHexWithInvalidCodes()
    {
        $this->assertFalse($this->validation->valid_hex('fff'));
        $this->assertFalse($this->validation->valid_hex('#ff'));
        $this->assertFalse($this->validation->valid_hex('#ffff'));
        $this->assertFalse($this->validation->valid_hex('#gggggg'));
        $this->assertFalse($this->validation->valid_hex('#12345g'));
        $this->assertFalse($this->validation->valid_hex(''));
        $this->assertFalse($this->validation->valid_hex(null));
    }

    /**
     * Test that unique method exists and is callable
     * Note: Full testing requires database setup
     */
    public function testUniqueMethodExists()
    {
        $this->assertTrue(method_exists($this->validation, 'unique'));
        $this->assertTrue(is_callable([$this->validation, 'unique']));
    }

    /**
     * Test that relation_checker method exists and is callable
     * Note: Full testing requires database setup
     */
    public function testRelationCheckerMethodExists()
    {
        $this->assertTrue(method_exists($this->validation, 'relation_checker'));
        $this->assertTrue(is_callable([$this->validation, 'relation_checker']));
    }

    /**
     * Test that validate_upload method exists and is callable
     * Note: Full testing requires file upload mocking and CodeIgniter services
     */
    public function testValidateUploadMethodExists()
    {
        $this->assertTrue(method_exists($this->validation, 'validate_upload'));
        $this->assertTrue(is_callable([$this->validation, 'validate_upload']));
    }
}
