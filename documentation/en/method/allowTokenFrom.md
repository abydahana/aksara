`allowTokenFrom()` accepts security tokens generated for additional URIs.

### Purpose
`allowTokenFrom()` accepts security tokens generated for additional URIs. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it near the beginning of a controller method to configure how Core handles the current request.

### Reference
`allowTokenFrom(string|array $uris = [])`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$uris` | <code>string&#124;array</code> | No | `[]` | URI or URI list accepted as token sources. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`allowTokenFrom()` stores request-level configuration on the controller. Call it before the permission, rendering, or form-processing step that depends on it.

> [!WARNING]
> Only allow tokens from routes that intentionally share the same form flow. A broad allowed URI list weakens CSRF isolation.

### Basic Usage
```php
$this->allowTokenFrom(['orders', 'orders/create']);

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
            ->allowTokenFrom(['orders', 'orders/create']);

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
* [setPermission](./setPermission)
* [validToken](./validToken)
* [permitUpsert](./permitUpsert)
* [allowPublicFormSubmission](./allowPublicFormSubmission)
* [restrictOnDemo](./restrictOnDemo)
