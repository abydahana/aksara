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
use Aksara\Laboratory\Core;

class CoreTest extends TestCase
{
    /**
     * Test that Core is an abstract class
     */
    public function testCoreIsAbstract()
    {
        $reflection = new \ReflectionClass(Core::class);
        $this->assertTrue($reflection->isAbstract());
    }

    /**
     * Test that Core uses Traits
     */
    public function testCoreUsesTrait()
    {
        $reflection = new \ReflectionClass(Core::class);
        $traits = $reflection->getTraitNames();

        $this->assertContains('Aksara\\Laboratory\\Traits', $traits);
    }



    /**
     * Test that Core has CRUD methods
     */
    public function testHasCrudMethods()
    {
        $reflection = new \ReflectionClass(Core::class);

        $this->assertTrue($reflection->hasMethod('insertData'));
        $this->assertTrue($reflection->hasMethod('updateData'));
        $this->assertTrue($reflection->hasMethod('deleteData'));
    }

    /**
     * Test that Core has rendering methods
     */
    public function testHasRenderingMethods()
    {
        $reflection = new \ReflectionClass(Core::class);

        $this->assertTrue($reflection->hasMethod('render'));
    }

    /**
     * Test that Core has configuration methods
     */
    public function testHasConfigurationMethods()
    {
        $reflection = new \ReflectionClass(Core::class);

        $this->assertTrue($reflection->hasMethod('setTheme'));
        $this->assertTrue($reflection->hasMethod('setTitle'));
        $this->assertTrue($reflection->hasMethod('setDescription'));
        $this->assertTrue($reflection->hasMethod('setIcon'));
    }

    /**
     * Test that Core has query builder methods
     */
    public function testHasQueryBuilderMethods()
    {
        $reflection = new \ReflectionClass(Core::class);

        $this->assertTrue($reflection->hasMethod('select'));
        $this->assertTrue($reflection->hasMethod('where'));
        $this->assertTrue($reflection->hasMethod('orderBy'));
        $this->assertTrue($reflection->hasMethod('limit'));
    }

    /**
     * Test that Core has field configuration methods
     */
    public function testHasFieldConfigurationMethods()
    {
        $reflection = new \ReflectionClass(Core::class);

        $this->assertTrue($reflection->hasMethod('setField'));
        $this->assertTrue($reflection->hasMethod('unsetField'));
        $this->assertTrue($reflection->hasMethod('fieldPosition'));
    }
}
