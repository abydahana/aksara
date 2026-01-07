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

use PHPUnit\Framework\TestCase;
use Aksara\Libraries\Html_dom;

class HtmlDomTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testLoadHtmlString()
    {
        $html = '<html><body><div id="test">Hello World</div><span class="foo">Bar</span></body></html>';

        $dom = new Html_dom($html);

        $div = $dom->find('#test', 0);
        $this->assertNotNull($div);
        $this->assertEquals('Hello World', $div->innertext);

        $span = $dom->find('.foo', 0);
        $this->assertNotNull($span);
        $this->assertEquals('Bar', $span->innertext);

        // Test non-existent
        $none = $dom->find('#nonexistent', 0);
        $this->assertNull($none);
    }

    public function testFindMultiple()
    {
        $html = '<ul><li>Item 1</li><li>Item 2</li></ul>';
        $dom = new Html_dom($html);

        $items = $dom->find('li');
        $this->assertCount(2, $items);
        $this->assertEquals('Item 1', $items[0]->innertext);
        $this->assertEquals('Item 2', $items[1]->innertext);
    }

    public function testAttributes()
    {
        $html = '<a href="https://example.com" title="Example">Link</a>';
        $dom = new Html_dom($html);

        $a = $dom->find('a', 0);
        $this->assertEquals('https://example.com', $a->href);
        $this->assertEquals('Example', $a->title);
    }
}
