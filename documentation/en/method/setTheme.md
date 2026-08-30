`setTheme()` selects the frontend or backend theme. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`setTheme()` selects the frontend or backend theme. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it near the beginning of a controller method to configure how Core handles the current request.

### Reference
`setTheme(string $theme = 'frontend')`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$theme` | `string` | No | `'frontend'` | Theme channel, usually `frontend` or `backend`. |

### Return Value
`static|bool`

Returns the current controller instance when the theme is accepted, or `false` when the requested theme cannot be used.

### Behavior
`setTheme()` stores request-level configuration on the controller. Call it before the permission, rendering, or form-processing step that depends on it.

### Basic Usage
```php
$this->setTheme('backend');

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
            ->setTheme('backend');

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
* [setTitle](./setTitle)
* [setDescription](./setDescription)
* [setIcon](./setIcon)
* [setBreadcrumb](./setBreadcrumb)
* [setOutput](./setOutput)
* [setTemplate](./setTemplate)
