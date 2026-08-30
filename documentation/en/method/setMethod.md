`setMethod()` overrides the active Core method. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`setMethod()` overrides the active Core method. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it near the beginning of a controller method to configure how Core handles the current request.

### Reference
`setMethod(string $method = 'index')`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$method` | `string` | No | `'index'` | Core method name such as `index`, `create`, `read`, `update`, or `delete`. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`setMethod()` stores request-level configuration on the controller. Call it before the permission, rendering, or form-processing step that depends on it.

### Basic Usage
```php
$this->setMethod('read');

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
            ->setMethod('read');

        return $this->render('orders');
    }
}
```

### Result
The controller stores the configuration and applies it later in the current request lifecycle.

### Notes
* This method is chainable and returns the current controller instance.
* Call configuration methods before `setPermission()` or `render()` when those steps depend on the configured value.
* Order matters: call `parentModule()` and `setMethod()` before `setPermission()` when needed.

### Common Mistakes
* Calling the method after the permission or render step that already needed it.
* Spreading related configuration across distant parts of the controller.

### Related Methods
* [render](./render)
