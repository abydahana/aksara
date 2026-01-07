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

if (! function_exists('recommendation_generator')) {
    /**
     * Table of content generator
     *
     * @param   string $content
     */
    function recommendation_generator($content = null, $recommendations = [], int $perParagraph = 5)
    {
        // Reformat recommendation object into array
        $recommendations = json_decode(json_encode($recommendations), true);

        // Split the text into paragraphs
        $paragraphs = explode('</p>', $content);
        $updatedContent = '';
        $applied = false;

        if (sizeof($paragraphs) < $perParagraph) {
            // Paragraph is lower than minimum, change default minimum setting
            $perParagraph = sizeof($paragraphs);
        }

        foreach ($paragraphs as $index => $paragraph) {
            // If the paragraph is not empty, add the closing </p> tag
            if (! empty(trim($paragraph))) {
                $paragraph .= "</p>";
            }

            // Add the paragraph to the updated text
            $updatedContent .= $paragraph;

            // Add additional content after every 5th paragraph
            if (0 == ($index + 1) % $perParagraph && ! empty(trim($paragraph)) && isset($recommendations[($index / $perParagraph)])) {
                $applied = true;
                $updatedContent .= '<div class="alert alert-info callout"><p class="mb-0">' . phrase('Peoples also read') . '</p><a href="' . $recommendations[($index / $perParagraph)]['link'] . '" class="--xhr">' . $recommendations[($index / $perParagraph)]['title'] . '</a></div>';
            }
        }

        if (! $applied && $recommendations) {
            $updatedContent .= '<div class="alert alert-info callout"><p class="mb-0">' . phrase('Peoples also read') . '</p><a href="' . $recommendations[0]['link'] . '" class="--xhr">' . $recommendations[0]['title'] . '</a></div>';
        }

        return $updatedContent;
    }
}

if (! function_exists('toc_generator')) {
    /**
     * Table of content generator
     *
     * @param   string $content
     */
    function toc_generator($content = null)
    {
        $toc = null; // Start the table of contents
        $pattern = '/<h([1-6])[^>]*>(.*?)<\/h\1>/i'; // Regex pattern to find headings (h1 to h6)
        $matches = [];

        // Find all headings in the content
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $key => $match) {
            $level = $match[1]; // Heading level (e.g., 1 for h1, 2 for h2)
            $title = $match[2]; // The text inside the heading
            $slug = format_slug($title); // Create a URL-friendly ID

            // Add ID attribute to the heading in the content
            $content = str_replace($match[0], "<h$level id=\"$slug\" class=\"fw-bold\">$title</h$level>", $content);

            // Add a list item to the TOC
            $toc .= "<li class=\"toc-level-$level\"><a href=\"#$slug\" class=\"lead\">$title</a></li>";
        }

        if ($toc) {
            $toc = '<ul class="mb-0">' . $toc . '</ul>';
        }

        return [$toc, $content];
    }
}
