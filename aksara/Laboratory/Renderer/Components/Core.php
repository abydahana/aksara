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

/**
 * Core Renderer Component
 *
 * This class is responsible for rendering the core UI components of the CMS.
 * It utilizes the Builder class to fetch templates and the Traits to manage
 * dynamic properties passed from the controller.
 */
class Core
{
    /**
     * Load traits to access dynamic properties (e.g., $_set_theme, $_set_title).
     */
    use Traits;

    /**
     * Instance of the UI Builder.
     */
    private Builder $builder;

    /**
     * Current request path/module path.
     */
    private ?string $path = null;

    /**
     * Database Model Instance.
     */
    private mixed $model = null;

    /**
     * API Client Status/Instance.
     */
    private mixed $api_client = null;

    /**
     * Constructor
     *
     * Hydrates the class properties dynamically based on the provided array.
     * This allows the renderer to inherit the state of the Controller/Core class.
     *
     * @param   array $properties Associative array of properties to inject
     */
    public function __construct(array $properties = [])
    {
        // Hydrate properties dynamically
        foreach ($properties as $key => $val) {
            // Check if property exists in class or trait before assignment to avoid dynamic property creation deprecation in PHP 8.2+
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }

        // Initialize the UI Builder
        $this->builder = new Builder();
    }

    /**
     * Render the Core Component.
     *
     * Retrieves the appropriate core template (e.g., index table wrapper)
     * based on the active theme.
     *
     * @param   array $serialized Data to be passed to the view (optional)
     * @param   int   $length     Length/Count of data (optional)
     * @return  mixed Returns the rendered component string or void
     */
    public function render(array $serialized = [], int $length = 0): mixed
    {
        // Retrieve the 'core' component template from the Builder
        // Uses $_set_theme (from Traits) to locate the correct theme folder
        $component = $this->builder->get_component($this->_set_theme, 'core');

        // Logic continues here...
        // (Assuming you will return or output the component later)
        return $component;
    }
}
