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
use Aksara\Laboratory\Traits;

class TraitsTest extends TestCase
{
    /**
     * Test that Traits can be used in a class
     */
    public function testTraitsCanBeUsedInClass()
    {
        $testClass = new class () {
            use Traits;
        };

        // Verify the anonymous class uses the Traits trait
        $reflection = new \ReflectionClass($testClass);
        $traits = $reflection->getTraitNames();

        $this->assertContains('Aksara\\Laboratory\\Traits', $traits);
    }

    /**
     * Test that Traits defines expected properties
     */
    public function testTraitsDefinesProperties()
    {
        $reflection = new \ReflectionClass(Traits::class);
        $properties = $reflection->getProperties();

        $this->assertGreaterThan(0, count($properties));
    }

    /**
     * Test that specific core properties exist in Traits
     */
    public function testCorePropertiesExist()
    {
        $reflection = new \ReflectionClass(Traits::class);

        // Check for some key properties (they are private, so we need to use getProperties)
        $propertyNames = array_map(fn ($prop) => $prop->getName(), $reflection->getProperties());

        $this->assertContains('_table', $propertyNames);
        $this->assertContains('_method', $propertyNames);
        $this->assertContains('_addClass', $propertyNames);
    }
}
