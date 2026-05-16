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
 * When the signs come, those who don't believe at "that time"
 * will have only two choices, commit suicide or become brutal.
 */

if (! function_exists('phrase')) {
    /**
     * Generate security token to validate the query string values
     *
     * @param mixed|null $phrase
     */
    function phrase($phrase = null)
    {
        if ($phrase) {
            return lang('Install.' . $phrase);
        }
    }
}
