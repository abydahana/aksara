`getMethod()` returns the active Core method name.

### Purpose
`getMethod()` returns the active Core method name. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it near the beginning of a controller method to configure how Core handles the current request.

### Reference
`getMethod()`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| None | - | - | - | This method does not accept parameters. |

### Return Value
`string`

Returns the active Core method name resolved from the current request.

### Behavior
`getMethod()` stores request-level configuration on the controller. Call it before the permission, rendering, or form-processing step that depends on it.

### Basic Usage
```php
if ('create' === $this->getMethod()) {
    $this->setTitle(phrase('Create Order'));
}
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
            ->getMethod();

        return $this->render('orders');
    }
}
```

### Result
The controller stores the configuration and applies it later in the current request lifecycle.

### Notes
* Call configuration methods before `setPermission()` or `render()` when those steps depend on the configured value.

### Common Mistakes
* Calling the method after the permission or render step that already needed it.
* Spreading related configuration across distant parts of the controller.

### Related Methods
* [render](./render)
