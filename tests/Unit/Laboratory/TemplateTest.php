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

namespace Tests\Unit\Laboratory;

use PHPUnit\Framework\TestCase;
use Aksara\Laboratory\Template;

class TemplateTest extends TestCase
{
    private Template $template;

    protected function setUp(): void
    {
        $this->template = new Template('frontend', 'index');
    }

    /**
     * Test that Template can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(Template::class, $this->template);
    }

    /**
     * Test that Template stores theme property
     */
    public function testStoresThemeProperty()
    {
        $this->assertEquals('frontend', $this->template->theme);
    }

    /**
     * Test that Template can be instantiated with different theme
     */
    public function testCanBeInstantiatedWithDifferentTheme()
    {
        $backendTemplate = new Template('backend', 'index');
        $this->assertEquals('backend', $backendTemplate->theme);
    }

    /**
     * Test that Template has core methods
     */
    public function testHasCoreMethods()
    {
        $this->assertTrue(method_exists($this->template, 'get_theme'));
        $this->assertTrue(method_exists($this->template, 'get_theme_property'));
        $this->assertTrue(method_exists($this->template, 'get_view'));
        $this->assertTrue(method_exists($this->template, 'build'));
    }

    /**
     * Test that Template has utility methods
     */
    public function testHasUtilityMethods()
    {
        $this->assertTrue(method_exists($this->template, 'breadcrumb'));
        $this->assertTrue(method_exists($this->template, 'pagination'));
    }

    /**
     * Test that get_theme method is callable
     */
    public function testGetThemeMethodIsCallable()
    {
        $this->assertTrue(is_callable([$this->template, 'get_theme']));
    }

    /**
     * Test that get_theme_property method is callable
     */
    public function testGetThemePropertyMethodIsCallable()
    {
        $this->assertTrue(is_callable([$this->template, 'get_theme_property']));
    }
}
