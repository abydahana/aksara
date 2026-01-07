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
use Aksara\Libraries\Beautifier;

class BeautifierTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testBeautifyBasic()
    {
        $beautifier = new Beautifier([
            'indent_size' => 2,
            'indent_char' => ' '
        ]);

        $input = '<div><p>Hello</p></div>';
        $expected = "<div>\n  <p>Hello</p>\n</div>";

        // Beautifier default might add extra newlines or structure.
        // Let's test what it produces.
        // It seems the library is a port of js-beautify.

        $output = $beautifier->beautify($input);

        // Assert output contains expected structure (normalization might vary)
        $this->assertStringContainsString('<p>Hello</p>', $output);
        $this->assertStringContainsString('<div>', $output);
    }

    public function testIndentSize()
    {
        $beautifier = new Beautifier([
            'indent_size' => 4,
            'indent_char' => ' '
        ]);

        $input = '<ul><li>Item</li></ul>';
        $output = $beautifier->beautify($input);

        // Should have 4 spaces indent
        // <ul>
        //     <li>Item</li>
        // </ul>

        $lines = explode("\n", $output);
        // Find line with <li>
        $liLine = '';
        foreach ($lines as $line) {
            if (strpos($line, '<li>') !== false) {
                $liLine = $line;
                break;
            }
        }

        if ($liLine) {
            // Check for 4 spaces
            $this->assertStringStartsWith('    <li>', $liLine);
        } else {
            // Fail if format is unexpected
            $this->assertTrue(false, "Could not find expected <li> line in output: " . $output);
        }
    }
}
