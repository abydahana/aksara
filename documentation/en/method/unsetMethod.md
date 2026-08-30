`unsetMethod()` disables one or more CRUD methods.

### Purpose
`unsetMethod()` disables one or more CRUD methods. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it near the beginning of a controller method to configure how Core handles the current request.

### Reference
`unsetMethod(array|string $params = [])`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$params` | <code>array&#124;string</code> | No | `[]` | String value or associative array of values, depending on the method. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`unsetMethod()` stores request-level configuration on the controller. Call it before the permission, rendering, or form-processing step that depends on it.

> [!WARNING]
>
> Disabling a method removes it from the generated Core flow, but custom public controller methods can still exist if you define them separately.

### Basic Usage
```php
$this->unsetMethod('delete, export');

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
            ->unsetMethod('delete, export');

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
* [unsetColumn](./unsetColumn)
* [unsetField](./unsetField)
* [unsetView](./unsetView)
* [unsetUpdate](./unsetUpdate)
* [unsetRead](./unsetRead)
* [unsetDelete](./unsetDelete)
* [unsetTruncate](./unsetTruncate)
