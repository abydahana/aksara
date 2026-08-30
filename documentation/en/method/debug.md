`debug()` prints internal Core state before rendering.

### Purpose
`debug()` prints internal Core state before rendering. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it near the beginning of a controller method to configure how Core handles the current request.

### Reference
`debug(?string $resultType = null)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$resultType` | `?string` | No | `null` | Debug output type, commonly `properties` or `params`. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`debug()` stores request-level configuration on the controller. Call it before the permission, rendering, or form-processing step that depends on it.

> [!WARNING]
>
> Avoid enabling debug output in production because it can expose query, request, or controller state.

### Basic Usage
```php
$this->debug('properties');

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
            ->debug('properties');

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
* [render](./render)
