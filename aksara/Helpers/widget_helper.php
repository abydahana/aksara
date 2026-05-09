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

if (! function_exists('comment_widget')) {
    /**
     * Load comment widget
     */
    function comment_widget(?array $params = []): string
    {
        return '<div role="widget" data-source="' . base_url('xhr/widget/comment', $params) . '"></div>';
    }
}
