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
use Aksara\Laboratory\Services\Theme;

class ThemeTest extends TestCase
{
    private Theme $theme;

    protected function setUp(): void
    {
        $this->theme = new Theme();
    }

    public function testSetAndGetTheme()
    {
        $this->theme->setTheme('backend');
        $this->assertEquals('backend', $this->theme->get_theme());
    }

    public function testSetAndGetTemplate()
    {
        $this->theme->setTemplate('form', 'custom_form');
        $this->assertEquals(['form' => 'custom_form'], $this->theme->get_template());

        $this->theme->setTemplate(['index' => 'custom_index']);
        $this->assertEquals(['form' => 'custom_form', 'index' => 'custom_index'], $this->theme->get_template());
    }

    public function testSetAndGetTitle()
    {
        $this->theme->setTitle('My Title', 'Fallback');
        $this->assertEquals(['index' => 'My Title'], $this->theme->get_title());
        $this->assertEquals('Fallback', $this->theme->get_title_fallback());

        $this->assertEquals('My Title', $this->theme->get_title_by_method('index'));
        $this->assertEquals('My Title', $this->theme->get_title_by_method('create')); // Falls back to index
    }

    public function testSetTitleByArray()
    {
        $this->theme->setTitle(['create' => 'Add New', 'index' => 'List']);
        $this->assertEquals('Add New', $this->theme->get_title_by_method('create'));
        $this->assertEquals('List', $this->theme->get_title_by_method('index'));
        $this->assertEquals('List', $this->theme->get_title_by_method('read')); // Falls back to index
    }

    public function testSetAndGetDescription()
    {
        $this->theme->setDescription('My Desc', 'Fallback Desc');
        $this->assertEquals(['index' => 'My Desc'], $this->theme->get_description());
        $this->assertEquals('Fallback Desc', $this->theme->get_description_fallback());

        $this->assertEquals('My Desc', $this->theme->get_description_by_method('index'));
    }

    public function testSetAndGetIcon()
    {
        $this->theme->setIcon('mdi mdi-home', 'mdi mdi-star');
        $this->assertEquals(['index' => 'mdi mdi-home'], $this->theme->get_icon());
        $this->assertEquals('mdi mdi-star', $this->theme->get_icon_fallback());

        $this->assertEquals('mdi mdi-home', $this->theme->get_icon_by_method('index'));
    }

    public function testSetBreadcrumb()
    {
        $this->theme->setBreadcrumb('url', 'Label');
        $this->assertEquals(['url' => 'Label'], $this->theme->get_breadcrumb());

        $this->theme->setBreadcrumb(['url2' => 'Label2']);
        $this->assertEquals(['url' => 'Label', 'url2' => 'Label2'], $this->theme->get_breadcrumb());
    }
}
