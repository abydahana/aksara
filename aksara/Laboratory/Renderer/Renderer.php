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

namespace Aksara\Laboratory\Renderer;

use Aksara\Laboratory\Traits;
use Aksara\Laboratory\Renderer\Components\Core;
use Aksara\Laboratory\Renderer\Components\Table;
use Aksara\Laboratory\Renderer\Components\Form;
use Aksara\Laboratory\Renderer\Components\View;

class Renderer
{
    /**
     * Load trait, get dynamic properties
     */
    use Traits;

    private $path;
    private $model;
    private $api_client;

    public function __construct()
    {
        // Safe abstraction
    }

    public function setProperty(array $properties)
    {
        foreach ($properties as $key => $val) {
            $this->$key = $val;
        }

        return $this;
    }

    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    public function render(array $serialized = [], int $length = 0)
    {
        if ('table' === $this->path) {
            // Use table component
            $component = new Table(get_object_vars($this));
        } elseif ('form' === $this->path) {
            // Use form component
            $component = new Form(get_object_vars($this));
        } elseif ('view' === $this->path) {
            // Use view component
            $component = new View(get_object_vars($this));
        } else {
            // Use core component
            $component = new Core(get_object_vars($this));
        }

        $component = $component->render($serialized, $length);

        return $component;
    }
}
