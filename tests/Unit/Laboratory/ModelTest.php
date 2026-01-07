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
use Aksara\Laboratory\Model;

class ModelTest extends TestCase
{
    private Model $model;

    protected function setUp(): void
    {
        $this->model = new Model();
    }

    /**
     * Test that Model can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(Model::class, $this->model);
    }

    /**
     * Test that Model has core query builder methods
     */
    public function testHasCoreQueryBuilderMethods()
    {
        $this->assertTrue(method_exists($this->model, 'select'));
        $this->assertTrue(method_exists($this->model, 'where'));
        $this->assertTrue(method_exists($this->model, 'get'));
        $this->assertTrue(method_exists($this->model, 'getWhere'));
        $this->assertTrue(method_exists($this->model, 'insert'));
        $this->assertTrue(method_exists($this->model, 'update'));
        $this->assertTrue(method_exists($this->model, 'delete'));
    }

    /**
     * Test that Model has utility methods
     */
    public function testHasUtilityMethods()
    {
        $this->assertTrue(method_exists($this->model, 'tableExists'));
        $this->assertTrue(method_exists($this->model, 'fieldExists'));
        $this->assertTrue(method_exists($this->model, 'listFields'));
        $this->assertTrue(method_exists($this->model, 'fieldData'));
    }

    /**
     * Test that Model has transaction methods
     */
    public function testHasTransactionMethods()
    {
        $this->assertTrue(method_exists($this->model, 'transStart'));
        $this->assertTrue(method_exists($this->model, 'transComplete'));
        $this->assertTrue(method_exists($this->model, 'transStatus'));
    }

    /**
     * Test that Model has result methods
     */
    public function testHasResultMethods()
    {
        $this->assertTrue(method_exists($this->model, 'result'));
        $this->assertTrue(method_exists($this->model, 'resultArray'));
        $this->assertTrue(method_exists($this->model, 'row'));
        $this->assertTrue(method_exists($this->model, 'rowArray'));
    }

    /**
     * Test that Model has limit and offset methods
     */
    public function testHasLimitAndOffsetMethods()
    {
        $this->assertTrue(method_exists($this->model, 'limit'));
        $this->assertTrue(method_exists($this->model, 'offset'));
    }
}
