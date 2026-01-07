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

namespace Tests\Unit\Libraries;

use CodeIgniter\Test\CIUnitTestCase;
use Aksara\Libraries\Document;

class DocumentTest extends CIUnitTestCase
{
    public function testInstantiation()
    {
        $doc = new Document();
        $this->assertInstanceOf(Document::class, $doc);
    }

    public function testPageSizeAndMarginChain()
    {
        $doc = new Document();
        // Just verify method chaining works and doesn't throw errors
        $result = $doc->pageSize('A4', 'landscape')
            ->pageMargin(10, 10, 10, 10);

        $this->assertInstanceOf(Document::class, $result);
    }
}
