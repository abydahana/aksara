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

namespace Aksara\Laboratory\Renderer\Components;

use Aksara\Laboratory\Traits;

use Aksara\Laboratory\Builder\Builder;

class Core
{
    /**
     * Load trait, get dynamic properties
     */
    use Traits;

    private $builder;
    private $path;
    private $model;
    private $api_client;

    public function __construct($properties = [])
    {
        foreach ($properties as $key => $val) {
            $this->$key = $val;
        }

        $this->builder = new Builder();
    }

    public function render(array $serialized = [], int $length = 0)
    {
        // Get or create component of matches last field type element
        $component = $this->builder->get_component($this->_set_theme, 'core');
    }
}
