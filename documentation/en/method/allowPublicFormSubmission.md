`allowPublicFormSubmission()` allows a public form to use the Core submission pipeline. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`allowPublicFormSubmission()` allows a public form to use the Core submission pipeline. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it near the beginning of a controller method to configure how Core handles the current request.

### Reference
`allowPublicFormSubmission(bool $return = true)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$return` | `bool` | No | `true` | Boolean flag that enables or disables the feature. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`allowPublicFormSubmission()` stores request-level configuration on the controller. Call it before the permission, rendering, or form-processing step that depends on it.

### Basic Usage
```php
$this->allowPublicFormSubmission();

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
            ->allowPublicFormSubmission();

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
* [allowTokenFrom](./allowTokenFrom)
* [permitUpsert](./permitUpsert)
* [restrictOnDemo](./restrictOnDemo)
