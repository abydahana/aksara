`setAiContext()` stores module-specific context for the AI assistant. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`setAiContext()` stores module-specific context for the AI assistant. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it near the beginning of a controller method to configure how Core handles the current request.

### Reference
`setAiContext(array $context)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$context` | `array` | Yes | `` | AI context array containing scope, instructions, max tokens, data, tone, or audience. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`setAiContext()` stores request-level configuration on the controller. Call it before the permission, rendering, or form-processing step that depends on it.

### Basic Usage
```php
$this->setAiContext(['scope' => 'product', 'instructions' => 'Use concise product descriptions.', 'max_tokens' => 2048]);

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
            ->setAiContext(['scope' => 'product', 'instructions' => 'Use concise product descriptions.', 'max_tokens' => 2048]);

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
