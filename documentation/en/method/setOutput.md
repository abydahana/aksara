`setOutput()` adds data to the view or API response.

### Purpose
`setOutput()` adds data to the view or API response. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it near the beginning of a controller method to configure how Core handles the current request.

### Reference
`setOutput(string|array $params = [], mixed $value = [])`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$params` | <code>string&#124;array</code> | No | `[]` | String value or associative array of values, depending on the method. |
| `$value` | `mixed` | No | `[]` | Value assigned to the given key or field. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`setOutput()` stores request-level configuration on the controller. Call it before the permission, rendering, or form-processing step that depends on it.

### Basic Usage
```php
$this->setOutput('summary', $this->model->getOrderSummary());

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
            ->setOutput('summary', $this->model->getOrderSummary());

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
* [setTemplate](./setTemplate)
* [setTheme](./setTheme)
