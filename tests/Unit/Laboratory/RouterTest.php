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

class RouterTest extends TestCase
{
    /**
     * Test that Router class exists
     */
    public function testRouterClassExists()
    {
        $this->assertTrue(class_exists(\Aksara\Laboratory\Router::class));
    }

    /**
     * Test that Router has expected private methods
     * Note: Full testing requires CodeIgniter services and routes object
     */
    public function testRouterHasExpectedMethods()
    {
        $reflection = new \ReflectionClass(\Aksara\Laboratory\Router::class);

        $this->assertTrue($reflection->hasMethod('_directoryRoute'));
        $this->assertTrue($reflection->hasMethod('_themeRoute'));
    }

    /**
     * Test that Router constructor exists
     */
    public function testRouterHasConstructor()
    {
        $reflection = new \ReflectionClass(\Aksara\Laboratory\Router::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertTrue($constructor->isPublic());
    }
}
