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
use Aksara\Laboratory\Services\Crud;
use stdClass;

class CrudTest extends TestCase
{
    private Crud $crud;
    private object $mockController;

    protected function setUp(): void
    {
        // Create a mock controller object with basic properties
        $this->mockController = new stdClass();
        $this->mockController->_table = 'test_table';
        $this->mockController->_primary = 'id';
        $this->mockController->_method = 'index';

        $this->crud = new Crud($this->mockController);
    }

    /**
     * Test that Crud class can be instantiated with a controller
     */
    public function testCanBeInstantiatedWithController()
    {
        $this->assertInstanceOf(Crud::class, $this->crud);
    }

    /**
     * Test that runQuery method exists
     * Note: Full testing requires Model and database setup
     */
    public function testRunQueryMethodExists()
    {
        $this->assertTrue(method_exists($this->crud, 'runQuery'));
        $this->assertTrue(is_callable([$this->crud, 'runQuery']));
    }

    /**
     * Test that fetch method exists
     * Note: Full testing requires Model and database setup
     */
    public function testFetchMethodExists()
    {
        $this->assertTrue(method_exists($this->crud, 'fetch'));
        $this->assertTrue(is_callable([$this->crud, 'fetch']));
    }

    /**
     * Test that getRelation method exists
     * Note: Full testing requires Model and database setup
     */
    public function testGetRelationMethodExists()
    {
        $this->assertTrue(method_exists($this->crud, 'getRelation'));
        $this->assertTrue(is_callable([$this->crud, 'getRelation']));
    }

    /**
     * Test that insertData method exists
     * Note: Full testing requires Model and database setup
     */
    public function testInsertDataMethodExists()
    {
        $this->assertTrue(method_exists($this->crud, 'insertData'));
        $this->assertTrue(is_callable([$this->crud, 'insertData']));
    }

    /**
     * Test that updateData method exists
     * Note: Full testing requires Model and database setup
     */
    public function testUpdateDataMethodExists()
    {
        $this->assertTrue(method_exists($this->crud, 'updateData'));
        $this->assertTrue(is_callable([$this->crud, 'updateData']));
    }

    /**
     * Test that deleteData method exists
     * Note: Full testing requires Model and database setup
     */
    public function testDeleteDataMethodExists()
    {
        $this->assertTrue(method_exists($this->crud, 'deleteData'));
        $this->assertTrue(is_callable([$this->crud, 'deleteData']));
    }

    /**
     * Test that sortTable method exists
     * Note: Full testing requires Model and database setup
     */
    public function testSortTableMethodExists()
    {
        $this->assertTrue(method_exists($this->crud, 'sortTable'));
        $this->assertTrue(is_callable([$this->crud, 'sortTable']));
    }

    /**
     * Test that unlinkFiles method exists
     * Note: Full testing requires filesystem operations
     */
    public function testUnlinkFilesMethodExists()
    {
        $this->assertTrue(method_exists($this->crud, 'unlinkFiles'));
        $this->assertTrue(is_callable([$this->crud, 'unlinkFiles']));
    }
}
