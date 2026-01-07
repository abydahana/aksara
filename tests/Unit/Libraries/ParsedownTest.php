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
use Aksara\Libraries\Parsedown;

class ParsedownTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testHeaderParsing()
    {
        $parsedown = new Parsedown();

        $markdown = '# Hello World';
        $html = $parsedown->text($markdown);

        $this->assertEquals('<h1>Hello World</h1>', $html);
    }

    public function testBoldAndItalic()
    {
        $parsedown = new Parsedown();

        $markdown = '**Bold** and *Italic*';
        $html = $parsedown->text($markdown);

        $this->assertStringContainsString('<strong>Bold</strong>', $html);
        $this->assertStringContainsString('<em>Italic</em>', $html);
    }

    public function testListParsing()
    {
        $parsedown = new Parsedown();

        $markdown = "- Item 1\n- Item 2";
        $html = $parsedown->text($markdown);

        $this->assertStringContainsString('<ul>', $html);
        $this->assertStringContainsString('<li>Item 1</li>', $html);
        $this->assertStringContainsString('<li>Item 2</li>', $html);
    }

    public function testCodeBlock()
    {
        $parsedown = new Parsedown();

        $markdown = "```php\necho 'Hello';\n```";
        $html = $parsedown->text($markdown);

        $this->assertStringContainsString('<pre><code class="language-php">', $html);
        $this->assertStringContainsString("echo 'Hello';", $html);
    }
}
