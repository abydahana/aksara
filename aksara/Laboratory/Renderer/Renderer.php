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

/**
 * Main Renderer Dispatcher
 *
 * This class acts as the central factory for initializing the correct UI Component
 * (Table, Form, View, or Core) based on the requested path/context and delegating
 * the rendering task.
 */
class Renderer
{
    /**
     * Load traits to access dynamic properties (context).
     */
    use Traits;

    /**
     * Current rendering path/context ('table', 'form', 'view', 'core').
     */
    private ?string $path = null;

    /**
     * Database Model Instance (injected).
     */
    private mixed $model = null;

    /**
     * API Client Status/Instance (injected).
     */
    private mixed $apiClient = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        // No initialization required. Properties are set via setProperty().
    }

    /**
     * Set dynamic properties inherited from the controller (hydration).
     *
     * @param   array $properties Associative array of properties to inject
     */
    public function setProperty(array $properties): self
    {
        // Dynamically assign properties to the instance
        foreach ($properties as $key => $val) {
            // Check if property exists in class or trait before assignment
            if (property_exists($this, $key)) {
                $this->$key = $val;
            } else {
                // Try camelCase
                $camelKey = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
                
                 // Handle private property convention (starts with _)
                if (strpos($key, '_') === 0 && strpos($camelKey, '_') !== 0) {
                     $camelKey = '_' . $camelKey;
                }
                
                if (property_exists($this, $camelKey)) {
                    $this->$camelKey = $val;
                }
            }
        }

        return $this;
    }

    /**
     * Set the rendering path/context.
     *
     * @param   string $path The context identifier ('table', 'form', 'view', etc.)
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Delegates the rendering task to the appropriate Component class.
     *
     * @param   array $serialized Data from the model
     * @param   int   $length     Length of data
     * @return  mixed Returns the processed component data array
     */
    public function render(array $serialized = [], int $length = 0): mixed
    {
        // Pass all current properties (including inherited traits) to the component class
        $properties = get_object_vars($this);

        // Determine which component to use based on the path
        switch ($this->path) {
            case 'table':
                $component = new Table($properties);
                break;
            case 'form':
                $component = new Form($properties);
                break;
            case 'view':
                $component = new View($properties);
                break;
            default:
                // Default fallback component
                $component = new Core($properties);
                break;
        }

        // Call the render method of the instantiated component
        return $component->render($serialized, $length);
    }
}
