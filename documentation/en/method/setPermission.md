`setPermission()` enforces module and method permissions. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`setPermission()` enforces module and method permissions. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it near the beginning of a controller method to configure how Core handles the current request.

### Reference
`setPermission(array|string $permissiveGroup = [], ?string $redirect = null)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$permissiveGroup` | `array|string` | No | `[]` | Allowed group IDs as an array or comma-separated string. Use `0` to allow every group. |
| `$redirect` | `?string` | No | `null` | Optional redirect URL when access is denied. |

### Return Value
`static|Response`

Returns the current controller instance when access is allowed, or an Aksara/CodeIgniter response when access is denied or redirected.

### Behavior
`setPermission()` stores request-level configuration on the controller. Call it before the permission, rendering, or form-processing step that depends on it.

### Basic Usage
```php
$this->setPermission([1, 2]);

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

use Aksara\Controllers\BaseController;

class Orders extends BaseController
{
    public function index()
    {
        $this->setTitle(phrase('Orders'))
            ->setPermission([1, 2]);

        return $this->render('orders');
    }
}
```

### Result
The controller stores the configuration and applies it later in the current request lifecycle.

### Notes
* Call configuration methods before `setPermission()` or `render()` when those steps depend on the configured value.
* Order matters: call `parentModule()` and `setMethod()` before `setPermission()` when needed.

### Common Mistakes
* Calling the method after the permission or render step that already needed it.
* Spreading related configuration across distant parts of the controller.

### Related Methods
* [validToken](./validToken)
* [allowTokenFrom](./allowTokenFrom)
* [permitUpsert](./permitUpsert)
* [allowPublicFormSubmission](./allowPublicFormSubmission)
* [restrictOnDemo](./restrictOnDemo)
