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
use Aksara\Laboratory\Services\Permission;

class PermissionTest extends TestCase
{
    private Permission $permission;

    protected function setUp(): void
    {
        $this->permission = new Permission();
    }

    /**
     * Test that Permission class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(Permission::class, $this->permission);
    }

    /**
     * Test that authorize method exists
     * Note: Full testing requires database, session, and CodeIgniter services
     */
    public function testAuthorizeMethodExists()
    {
        $this->assertTrue(method_exists($this->permission, 'authorize'));
        $this->assertTrue(is_callable([$this->permission, 'authorize']));
    }

    /**
     * Test that allow method exists
     * Note: Full testing requires database and session setup
     */
    public function testAllowMethodExists()
    {
        $this->assertTrue(method_exists($this->permission, 'allow'));
        $this->assertTrue(is_callable([$this->permission, 'allow']));
    }

    /**
     * Test that restrict method exists
     * Note: Full testing requires database and session setup
     */
    public function testRestrictMethodExists()
    {
        $this->assertTrue(method_exists($this->permission, 'restrict'));
        $this->assertTrue(is_callable([$this->permission, 'restrict']));
    }

    /**
     * Test that must_ajax method exists
     * Note: Full testing requires request service
     */
    public function testMustAjaxMethodExists()
    {
        $this->assertTrue(method_exists($this->permission, 'must_ajax'));
        $this->assertTrue(is_callable([$this->permission, 'must_ajax']));
    }
}
