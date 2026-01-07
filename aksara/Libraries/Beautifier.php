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

namespace Aksara\Libraries;

/**
 * Beautify_Html class
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2007-2013 Einar Lielmanis and contributors.
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation files
 * (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge,
 * publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
 * BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * PHP port by Ivan Weiler, 2014
 */
class Beautifier
{
    private $options;

    private $pos;
    private $currentMode;
    private $tags;
    private $tagType;
    private $tokenText;
    private $lastToken;
    private $lastText;
    private $tokenType;
    private $newlines;
    private $indentContent;
    private $indentLevel;
    private $lineCharCount;
    private $indentString;
    private $cssBeautify;
    private $jsBeautify;
    private $input;
    private $inputLength;
    private $output;

    private $whitespace = ["\n", "\r", "\t", " "];

    //all the single tags for HTML
    private $singleToken = [
        'br', 'input', 'link', 'meta', '!doctype', 'basefont', 'base', 'area',
        'hr', 'wbr', 'param', 'img', 'isindex', '?xml', 'embed', '?php', '?', '?='
    ];

    //for tags that need a line of whitespace before them
    private $extraLiners = ['head', 'body', '/html'];

    public function __construct($options = [], $cssBeautify = null, $jsBeautify = null)
    {
        $this->set_options($options);

        $this->cssBeautify = ($cssBeautify && is_callable($cssBeautify)) ? $cssBeautify : false;
        $this->jsBeautify = ($jsBeautify && is_callable($jsBeautify)) ? $jsBeautify : false;

        $this->pos = 0; //Parser position
        $this->currentMode = 'CONTENT'; //reflects the current Parser mode: TAG/CONTENT

        //An object to hold tags, their position, and their parent-tags, initiated with default values
        $this->tags = [
            'parent' => 'parent1',
            'parentcount' => 1,
            'parent1' => ''
        ];

        $this->tagType = '';
        $this->tokenText = $this->lastToken = $this->lastText = $this->tokenType = '';
        $this->newlines = 0;

        $this->indentContent = $this->options['indent_inner_html'];
        $this->indentLevel = 0;
        $this->lineCharCount = 0; //count to see if wrap_line_length was exceeded
        $this->indentString = str_repeat($this->options['indent_char'], $this->options['indent_size']);
    }

    public function set_options($options)
    {
        if (isset($options['indent_inner_html'])) {
            $this->options['indent_inner_html'] = (bool)$options['indent_inner_html'];
        } else {
            $this->options['indent_inner_html'] = false;
        }

        if (isset($options['indent_size'])) {
            $this->options['indent_size'] = (int)$options['indent_size'];
        } else {
            $this->options['indent_size'] = 4;
        }

        if (isset($options['indent_char'])) {
            $this->options['indent_char'] = (string)$options['indent_char'];
        } else {
            $this->options['indent_char'] = ' ';
        }

        if (isset($options['indent_scripts']) && in_array($options['indent_scripts'], ['keep', 'separate', 'normal'], true)) {
            $this->options['indent_scripts'] = $options['indent_scripts'];
        } else {
            $this->options['indent_scripts'] = 'normal';
        }

        if (isset($options['wrap_line_length'])) {
            $this->options['wrap_line_length'] = (int)$options['wrap_line_length'];
        } else {
            $this->options['wrap_line_length'] = 32786;
        }

        if (isset($options['unformatted']) && is_array($options['unformatted'])) {
            $this->options['unformatted'] = $options['unformatted'];
        } else {
            $this->options['unformatted'] = [
                'a', 'span', 'bdo', 'em', 'strong', 'dfn', 'code', 'samp', 'kbd', 'var', 'cite', 'abbr',
                'acronym', 'q', 'sub', 'sup', 'tt', 'i', 'b', 'big', 'small', 'u', 's', 'strike',
                'font', 'ins', 'del', 'pre', 'address', 'dt', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
            ];
        }

        if (isset($options['preserve_newlines'])) {
            $this->options['preserve_newlines'] = (bool)$options['preserve_newlines'];
        } else {
            $this->options['preserve_newlines'] = true;
        }

        if ($this->options['preserve_newlines'] && isset($options['max_preserve_newlines'])) {
            $this->options['max_preserve_newlines'] = (int)$options['max_preserve_newlines'];
        } else {
            $this->options['max_preserve_newlines'] = 0;
        }
    }

    private function traverse_whitespace()
    {
        $inputChar = isset($this->input[$this->pos]) ? $this->input[$this->pos] : '';
        if ($inputChar && in_array($inputChar, $this->whitespace, true)) {
            $this->newlines = 0;
            while ($inputChar && in_array($inputChar, $this->whitespace, true)) {
                if ($this->options['preserve_newlines'] &&
                        "\n" === $inputChar &&
                        $this->newlines <= $this->options['max_preserve_newlines']) {
                    $this->newlines += 1;
                }

                $this->pos++;
                $inputChar = isset($this->input[$this->pos]) ? $this->input[$this->pos] : '';
            }
            return true;
        }
        return false;
    }

    //function to capture regular content between tags
    private function get_content()
    {
        $inputChar = '';
        $content = [];
        $space = false; //if a space is needed

        while (isset($this->input[$this->pos]) && '<' !== $this->input[$this->pos]) {
            if ($this->pos >= $this->inputLength) {
                return count($content) ? implode('', $content) : ['', 'TK_EOF'];
            }

            if ($this->traverse_whitespace()) {
                if (count($content)) {
                    $space = true;
                }
                continue; //don't want to insert unnecessary space
            }

            $inputChar = $this->input[$this->pos];
            $this->pos++;

            if ($space) {
                if ($this->lineCharCount >= $this->options['wrap_line_length']) { //insert a line when the wrap_line_length is reached
                    $this->print_newline(false, $content);
                    $this->print_indentation($content);
                } else {
                    $this->lineCharCount++;
                    $content[] = ' ';
                }
                $space = false;
            }
            $this->lineCharCount++;
            $content[] = $inputChar; //letter at-a-time (or string) inserted to an array
        }

        return count($content) ? implode('', $content) : '';
    }

    //get the full content of a script or style to pass to js_beautify
    private function get_contents_to($name)
    {
        if ($this->pos === $this->inputLength) {
            return ['', 'TK_EOF'];
        }
        $inputChar = '';
        $content = '';

        $regArray = [];
        preg_match('#</' . preg_quote($name, '#') . '\\s*>#im', $this->input, $regArray, PREG_OFFSET_CAPTURE, $this->pos);
        $endScript = $regArray ? ($regArray[0][1]) : $this->inputLength; //absolute end of script

        if ($this->pos < $endScript) { //get everything in between the script tags
            $content = substr($this->input, $this->pos, max($endScript - $this->pos, 0));
            $this->pos = $endScript;
        }

        return $content;
    }

    //function to record a tag and its parent in this.tags Object
    private function record_tag($tag)
    {
        if (isset($this->tags[$tag . 'count'])) { //check for the existence of this tag type
            $this->tags[$tag . 'count']++;
            $this->tags[$tag . $this->tags[$tag . 'count']] = $this->indentLevel; //and record the present indent level
        } else { //otherwise initialize this tag type
            $this->tags[$tag . 'count'] = 1;
            $this->tags[$tag . $this->tags[$tag . 'count']] = $this->indentLevel; //and record the present indent level
        }
        $this->tags[$tag . $this->tags[$tag . 'count'] . 'parent'] = $this->tags['parent']; //set the parent (i.e. in the case of a div this.tags.div1parent)
        $this->tags['parent'] = $tag . $this->tags[$tag . 'count']; //and make this the current parent (i.e. in the case of a div 'div1')
    }


    //function to retrieve the opening tag to the corresponding closer
    private function retrieve_tag($tag)
    {
        if (isset($this->tags[$tag . 'count'])) { //if the openener is not in the Object we ignore it
            $tempParent = $this->tags['parent']; //check to see if it's a closable tag.
            while ($tempParent) { //till we reach '' (the initial value);
                if ($tag . $this->tags[$tag . 'count'] === $tempParent) { //if this is it use it
                    break;
                }
                $tempParent = isset($this->tags[$tempParent . 'parent']) ? $this->tags[$tempParent . 'parent'] : ''; //otherwise keep on climbing up the DOM Tree
            }
            if ($tempParent) { //if we caught something
                $this->indentLevel = $this->tags[$tag . $this->tags[$tag . 'count']]; //set the indent_level accordingly
                $this->tags['parent'] = $this->tags[$tempParent . 'parent']; //and set the current parent
            }
            unset($this->tags[$tag . $this->tags[$tag . 'count'] . 'parent']); //delete the closed tags parent reference...
            unset($this->tags[$tag . $this->tags[$tag . 'count']]); //...and the tag itself
            if ($this->tags[$tag . 'count'] === 1) {
                unset($this->tags[$tag . 'count']);
            } else {
                $this->tags[$tag . 'count']--;
            }
        }
    }

    private function indent_to_tag($tag)
    {
        // Match the indentation level to the last use of this tag, but don't remove it.
        if (! $this->tags[$tag . 'count']) {
            return;
        }
        $tempParent = $this->tags['parent'];
        while ($tempParent) {
            if ($tag . $this->tags[$tag . 'count'] === $tempParent) {
                break;
            }
            $tempParent = $this->tags[$tempParent . 'parent'];
        }
        if ($tempParent) {
            $this->indentLevel = $this->tags[$tag . $this->tags[$tag . 'count']];
        }
    }

    //function to get a full tag and parse its type
    private function get_tag($peek = false)
    {
        $inputChar = '';
        $content = [];
        $comment = '';
        $space = false;
        $tagStart;
        $tagEnd;
        $tagStartChar = false;
        $origPos = $this->pos;
        $origLineCharCount = $this->lineCharCount;

        do {
            if ($this->pos >= $this->inputLength) {
                if ($peek) {
                    $this->pos = $origPos;
                    $this->lineCharCount = $origLineCharCount;
                }
                return count($content) ? implode('', $content) : ['', 'TK_EOF'];
            }

            $inputChar = $this->input[$this->pos];
            $this->pos++;

            if (in_array($inputChar, $this->whitespace, true)) { //don't want to insert unnecessary space
                $space = true;
                continue;
            }

            if ("'" === $inputChar || '"' === $inputChar) {
                $inputChar .= $this->get_unformatted($inputChar);
                $space = true;
            }

            if ('=' === $inputChar) { //no space before =
                $space = false;
            }

            if (count($content) && $content[count($content) - 1] !== '=' && '>' !== $inputChar && $space) {
                //no space after = or before >
                if ($this->lineCharCount >= $this->options['wrap_line_length']) {
                    $this->print_newline(false, $content);
                    $this->print_indentation($content);
                } else {
                    $content[] = ' ';
                    $this->lineCharCount++;
                }
                $space = false;
            }

            if ('<' === $inputChar && ! $tagStartChar) {
                $tagStart = $this->pos - 1;
                $tagStartChar = '<';
            }

            $this->lineCharCount++;
            $content[] = $inputChar; //inserts character at-a-time (or string)

            if (isset($content[1]) && '!' === $content[1]) { //if we're in a comment, do something special
                // We treat all comments as literals, even more than preformatted tags
                    $content = [$this->get_comment($tagStart)];
                break;
            }
        } while ('>' !== $inputChar);

        $tagComplete = implode('', $content);

        if (strpos($tagComplete, ' ') !== false) { //if there's whitespace, thats where the tag name ends
            $tagIndex = strpos($tagComplete, ' ');
        } else { //otherwise go with the tag ending
            $tagIndex = strpos($tagComplete, '>');
        }
        if ('<' === $tagComplete[0]) {
            $tagOffset = 1;
        } else {
            $tagOffset = '#' === $tagComplete[2] ? 3 : 2;
        }
        $tagCheck = strtolower(substr($tagComplete, $tagOffset, max($tagIndex - $tagOffset, 0)));

        if ($tagComplete[strlen($tagComplete) - 2] === '/' ||
            in_array($tagCheck, $this->singleToken, true)) { //if this tag name is a single tag type (either in the list or has a closing /)
            if (! $peek) {
                $this->tagType = 'SINGLE';
            }
        } elseif ('script' === $tagCheck /*&&
            (strpos($tagComplete, 'type') === false ||
            (strpos($tagComplete, 'type') !== false &&
            preg_match('/\b(text|application)\/(x-)?(javascript|ecmascript|jscript|livescript)/', $tagComplete)))*/
        ) {
            if (! $peek) {
                $this->record_tag($tagCheck);
                $this->tagType = 'SCRIPT';
            }
        } elseif ('style' === $tagCheck /*&&
            (strpos($tagComplete, 'type') === false ||
            (strpos($tagComplete, 'type') !==false && strpos($tagComplete, 'text/css') !== false))*/
        ) {
            if (! $peek) {
                $this->record_tag($tagCheck);
                $this->tagType = 'STYLE';
            }
        } elseif ($this->is_unformatted($tagCheck)) { // do not reformat the "unformatted" tags
            $comment = $this->get_unformatted('</' . $tagCheck . '>', $tagComplete); //...delegate to get_unformatted function
            $content[] = $comment;

            // Preserve collapsed whitespace either before or after this tag.
            if ($tagStart > 0 && in_array($this->input[$tagStart - 1], $this->whitespace, true)) {
                array_splice($content, 0, 0, $this->input[$tagStart - 1]);
            }
            $tagEnd = $this->pos - 1;
            if (in_array($this->input[$tagEnd + 1], $this->whitespace, true)) {
                $content[] = $this->input[$tagEnd + 1];
            }
            $this->tagType = 'SINGLE';
        } elseif ($tagCheck && '!' === $tagCheck[0]) { //peek for <! comment
            // for comments content is already correct.
                if (! $peek) {
                    $this->tagType = 'SINGLE';
                    $this->traverse_whitespace();
                }
        } elseif (! $peek) {
            if ($tagCheck && '/' === $tagCheck[0]) { //this tag is a double tag so check for tag-ending
                $this->retrieve_tag(substr($tagCheck, 1)); //remove it and all ancestors
                $this->tagType = 'END';
                $this->traverse_whitespace();
            } else { //otherwise it's a start-tag
                $this->record_tag($tagCheck); //push it on the tag stack
                if (strtolower($tagCheck) !== 'html') {
                    $this->indentContent = true;
                }
                $this->tagType = 'START';

                // Allow preserving of newlines after a start tag
                $this->traverse_whitespace();
            }
            if (in_array($tagCheck, $this->extraLiners, true)) { //check if this double needs an extra line
                $this->print_newline(false, $this->output);
                if (count($this->output) && $this->output[count($this->output) - 2] !== "\n") {
                    $this->print_newline(true, $this->output);
                }
            }
        }

        if ($peek) {
            $this->pos = $origPos;
            $this->lineCharCount = $origLineCharCount;
        }

        return implode('', $content); //returns fully formatted tag
    }

    //function to return comment content in its entirety
    private function get_comment($startPos)
    {
        // this is will have very poor perf, but will work for now.
        $comment = '';
        $delimiter = '>';
        $matched = false;

        $this->pos = $startPos;
        $inputChar = $this->input[$this->pos];
        $this->pos++;

        while ($this->pos <= $this->inputLength) {
            $comment .= $inputChar;

            // only need to check for the delimiter if the last chars match
            if ($comment[strlen($comment) - 1] === $delimiter[strlen($delimiter) - 1] &&
                strpos($comment, $delimiter) !== false) {
                break;
            }

            // only need to search for custom delimiter for the first few characters
            if (! $matched && strlen($comment) < 10) {
                if (strpos($comment, '<![if') === 0) { //peek for <![if conditional comment
                    $delimiter = '<![endif]>';
                    $matched = true;
                } elseif (strpos($comment, '<![cdata[') === 0) { //if it's a <[cdata[ comment...
                    $delimiter = ']]>';
                    $matched = true;
                } elseif (strpos($comment, '<![') === 0) { // some other ![ comment? ...
                    $delimiter = ']>';
                    $matched = true;
                } elseif (strpos($comment, '<!--') === 0) { // <!-- comment ...
                    $delimiter = '-->';
                    $matched = true;
                }
            }

            $inputChar = $this->input[$this->pos];
            $this->pos++;
        }

        return $comment;
    }

    //function to return unformatted content in its entirety
    private function get_unformatted($delimiter, $origTag = false)
    {
        if ($origTag && strpos(strtolower($origTag), $delimiter) !== false) {
            return '';
        }

        $inputChar = '';
        $content = '';
        $minIndex = 0;
        $space = true;

        do {
            if ($this->pos >= $this->inputLength) {
                return $content;
            }

            $inputChar = $this->input[$this->pos];
            $this->pos++;

            if (in_array($inputChar, $this->whitespace, true)) {
                if (! $space) {
                    $this->lineCharCount--;
                    continue;
                }
                if ("\n" === $inputChar || "\r" === $inputChar) {
                    $content .= "\n";
                    /*  Don't change tab indention for unformatted blocks.  If using code for html editing, this will greatly affect <pre> tags if they are specified in the 'unformatted array'
                    for ($i = 0; $i < $this->indentLevel; i++) {
                      $content .= $this->indentString;
                    }
                    $space = false; //...and make sure other indentation is erased
                    */
                    $this->lineCharCount = 0;
                    continue;
                }
            }
            $content .= $inputChar;
            $this->lineCharCount++;
            $space = true;

            /**
             * Assuming Base64 This method could possibly be applied to All Tags
             * but Base64 doesn't have " or ' as part of its data
             * so it is safe to look for the Next delimiter to find the end of the data
             * instead of reading Each character one at a time.
             */
            if (preg_match('/^data:image\/(bmp|gif|jpeg|png|svg\+xml|tiff|x-icon);base64$/', $content)) {
                $content .= substr($this->input, $this->pos, strpos($this->input, $delimiter, $this->pos) - $this->pos);

                $this->lineCharCount = strpos($this->input, $delimiter, $this->pos) - $this->pos;

                $this->pos = strpos($this->input, $delimiter, $this->pos);

                continue;
            }
        } while (strpos(strtolower($content), $delimiter, $minIndex) === false);

        return $content;
    }

    //initial handler for token-retrieval
    private function get_token()
    {
        if ('TK_TAG_SCRIPT' === $this->lastToken || 'TK_TAG_STYLE' === $this->lastToken) { //check if we need to format javascript
            $type = substr($this->lastToken, 7);
            $token = $this->get_contents_to($type);
            if (! is_string($token)) {
                return $token;
            }
            return [$token, 'TK_' . $type];
        }
        if ('CONTENT' === $this->currentMode) {
            $token = $this->get_content();

            if (! is_string($token)) {
                return $token;
            } else {
                return [$token, 'TK_CONTENT'];
            }
        }

        if ('TAG' === $this->currentMode) {
            $token = $this->get_tag();

            if (! is_string($token)) {
                return $token;
            } else {
                $tagNameType = 'TK_TAG_' . $this->tagType;
                return [$token, $tagNameType];
            }
        }
    }

    private function get_full_indent($level)
    {
        $level = $this->indentLevel + $level || 0;
        if ($level < 1) {
            return '';
        }

        return str_repeat($this->indentString, $level);
    }

    private function is_unformatted($tagCheck)
    {
        //is this an HTML5 block-level link?
        if (! in_array($tagCheck, $this->options['unformatted'], true)) {
            return false;
        }

        if (strtolower($tagCheck) !== 'a' || ! in_array('a', $this->options['unformatted'], true)) {
            return true;
        }

        //at this point we have an  tag; is its first child something we want to remain unformatted?
        $nextTag = $this->get_tag(true /* peek. */);

        // test next_tag to see if it is just html tag (no external content)
        $matches = [];
        preg_match('/^\s*<\s*\/?([a-z]*)\s*[^>]*>\s*$/', ($nextTag ? $nextTag : ""), $matches);
        $tag = $matches ? $matches : null;

        // if next_tag comes back but is not an isolated tag, then
        // let's treat the 'a' tag as having content
        // and respect the unformatted option
        if (! $tag || in_array($tag, $this->options['unformatted'], true)) {
            return true;
        } else {
            return false;
        }
    }

    private function print_newline($force, &$arr)
    {
        $this->lineCharCount = 0;
        if (! $arr || ! count($arr)) {
            return;
        }
        if ($force || ($arr[count($arr) - 1] !== "\n")) { //we might want the extra line
            $arr[] = "\n";
        }
    }

    private function print_indentation(&$arr)
    {
        for ($i = 0; $i < $this->indentLevel; $i++) {
            $arr[] = $this->indentString;
            $this->lineCharCount += strlen($this->indentString);
        }
    }

    private function print_token($text)
    {
        if ($text || '' !== $text) {
            if (count($this->output) && $this->output[count($this->output) - 1] === "\n") {
                $this->print_indentation($this->output);
                $text = ltrim($text);
            }
        }
        $this->print_token_raw($text);
    }

    private function print_token_raw($text)
    {
        if ($text && '' !== $text) {
            if (strlen($text) > 1 && $text[strlen($text) - 1] === "\n") {
                // unformatted tags can grab newlines as their last character
                $this->output[] = substr($text, 0, -1);
                $this->print_newline(false, $this->output);
            } else {
                $this->output[] = $text;
            }
        }

        for ($n = 0; $n < $this->newlines; $n++) {
            $this->print_newline($n > 0, $this->output);
        }
        $this->newlines = 0;
    }

    private function indent()
    {
        $this->indentLevel++;
    }

    private function unindent()
    {
        if ($this->indentLevel > 0) {
            $this->indentLevel--;
        }
    }

    public function beautify($input)
    {
        $this->input = $input; //gets the input for the Parser
        $this->inputLength = strlen($this->input);
        $this->output = [];

        while (true) {
            $t = $this->get_token();

            $this->tokenText = $t[0];
            $this->tokenType = $t[1];

            if ('TK_EOF' === $this->tokenType) {
                break;
            }

            switch ($this->tokenType) {
                case 'TK_TAG_START':
                    $this->print_newline(false, $this->output);
                    $this->print_token($this->tokenText);
                    if ($this->indentContent) {
                        $this->indent();
                        $this->indentContent = false;
                    }
                    $this->currentMode = 'CONTENT';
                    break;
                case 'TK_TAG_STYLE':
                case 'TK_TAG_SCRIPT':
                    $this->print_newline(false, $this->output);
                    $this->print_token($this->tokenText);
                    $this->currentMode = 'CONTENT';
                    break;
                case 'TK_TAG_END':
                    //Print new line only if the tag has no content and has child
                    if ('TK_CONTENT' === $this->lastToken && '' === $this->lastText) {
                        $matches = [];
                        preg_match('/\w+/', $this->tokenText, $matches);
                        $tagName = isset($matches[0]) ? $matches[0] : null;

                        $tagExtractedFromLastOutput = null;
                        if (count($this->output)) {
                            $matches = [];
                            preg_match('/(?:<|{{#)\s*(\w+)/', $this->output[count($this->output) - 1], $matches);
                            $tagExtractedFromLastOutput = isset($matches[0]) ? $matches[0] : null;
                        }
                        if (null === $tagExtractedFromLastOutput || $tagExtractedFromLastOutput[1] !== $tagName) {
                            $this->print_newline(false, $this->output);
                        }
                    }
                    $this->print_token($this->tokenText);
                    $this->currentMode = 'CONTENT';
                    break;
                case 'TK_TAG_SINGLE':
                    // Don't add a newline before elements that should remain unformatted.
                    $matches = [];
                    preg_match('/^\s*<([a-z]+)/i', $this->tokenText, $matches);
                    $tagCheck = $matches ? $matches : null;

                    if (! $tagCheck || ! in_array($tagCheck[1], $this->options['unformatted'], true)) {
                        $this->print_newline(false, $this->output);
                    }
                    $this->print_token($this->tokenText);
                    $this->currentMode = 'CONTENT';
                    break;
                case 'TK_CONTENT':
                    $this->print_token($this->tokenText);
                    $this->currentMode = 'TAG';
                    break;
                case 'TK_STYLE':
                case 'TK_SCRIPT':
                    if ('' !== $this->tokenText) {
                        $this->print_newline(false, $this->output);
                        $text = $this->tokenText;
                        $_beautifier = false;
                        $scriptIndentLevel = 1;

                        if ('TK_SCRIPT' === $this->tokenType) {
                            $_beautifier = $this->jsBeautify;
                        } elseif ('TK_STYLE' === $this->tokenType) {
                            $_beautifier = $this->cssBeautify;
                        }

                        if ("keep" === $this->options['indent_scripts']) {
                            $scriptIndentLevel = 0;
                        } elseif ("separate" === $this->options['indent_scripts']) {
                            $scriptIndentLevel = -$this->indentLevel;
                        }

                        $indentation = $this->get_full_indent($scriptIndentLevel);
                        if ($_beautifier) {
                            // call the Beautifier if avaliable
                            $text = $_beautifier(preg_replace('/^\s*/', $indentation, $text), $this->options);
                        } else {
                            // simply indent the string otherwise

                            $matches = [];
                            preg_match('/^\s*/', $text, $matches);
                            $white = isset($matches[0]) ? $matches[0] : null;

                            $matches = [];
                            preg_match('/[^\n\r]*$/', $white, $matches);
                            $dummy = isset($matches[0]) ? $matches[0] : null;

                            $_level = count(explode($this->indentString, $dummy)) - 1;
                            $reindent = $this->get_full_indent($scriptIndentLevel - $_level);

                            $text = preg_replace('/^\s*/', $indentation, $text);
                            $text = preg_replace('/\r\n|\r|\n/', "\n" . $reindent, $text);
                            $text = preg_replace('/\s+$/', '', $text);
                        }

                        if ($text) {
                            $this->print_token_raw($indentation . trim($text));
                            $this->print_newline(false, $this->output);
                        }
                    }
                    $this->currentMode = 'TAG';
                    break;
            }

            $this->lastToken = $this->tokenType;
            $this->lastText = $this->tokenText;
        }

        return implode('', $this->output);
    }
}
