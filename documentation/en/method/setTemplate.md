`setTemplate()` passes custom options to the template renderer. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`setTemplate()` passes custom options to the template renderer. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it near the beginning of a controller method to configure how Core handles the current request.

### Reference
`setTemplate(array|string $params = [], ?string $value = null)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$params` | `array\|string` | No | `[]` | String value or associative array of values, depending on the method. |
| `$value` | `?string` | No | `null` | Value assigned to the given key or field. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`setTemplate()` stores request-level configuration on the controller. Call it before the permission, rendering, or form-processing step that depends on it.

### Basic Usage
```php
$this->setTemplate('toolbar', false);

return $this->render('orders');
```

### Advanced Usage
```php
$this->setTitle(phrase('Orders'))
    ->setIcon('mdi mdi-cart-outline')
    ->setPermission();
```

### Complete Example
```php
namespace Modules\Orders\Controllers;

use Aksara\Laboratory\Core;

class Orders extends Core
{
    public function index()
    {
        $this->setTitle(phrase('Orders'))
            ->setTemplate('toolbar', false);

        return $this->render('orders');
    }
}
```

### Result
The controller stores the configuration and applies it later in the current request lifecycle.

### Notes
* This method is chainable and returns the current controller instance.
* Call configuration methods before `setPermission()` or `render()` when those steps depend on the configured value.

### Common Mistakes
* Calling the method after the permission or render step that already needed it.
* Spreading related configuration across distant parts of the controller.

### Related Methods
* [setTitle](./setTitle)
* [setDescription](./setDescription)
* [setIcon](./setIcon)
* [setBreadcrumb](./setBreadcrumb)
* [setOutput](./setOutput)
* [setTheme](./setTheme)
