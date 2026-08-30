`validToken()` validates a submitted security token. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`validToken()` validates a submitted security token. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it near the beginning of a controller method to configure how Core handles the current request.

### Reference
`validToken(?string $token, string|array $allowedUris = [])`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$token` | `?string` | Yes | - | Submitted token value. |
| `$allowedUris` | `string\|array` | No | `[]` | Additional URI or URI list whose token may be accepted. |

### Return Value
`bool`

Returns `true` when the submitted token is accepted for the current route or allowed URI; otherwise returns `false`.

### Behavior
`validToken()` stores request-level configuration on the controller. Call it before the permission, rendering, or form-processing step that depends on it.

### Basic Usage
```php
if (! $this->validToken($this->request->getPost('_token'), ['orders/create'])) {
    return throw_exception(403, phrase('The security token is invalid or expired.'));
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
            ->validToken();

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
* [setPermission](./setPermission)
* [allowTokenFrom](./allowTokenFrom)
* [permitUpsert](./permitUpsert)
* [allowPublicFormSubmission](./allowPublicFormSubmission)
* [restrictOnDemo](./restrictOnDemo)
