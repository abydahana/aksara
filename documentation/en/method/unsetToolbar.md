`unsetToolbar()` removes toolbar actions from the generated interface. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`unsetToolbar()` removes toolbar actions from the generated interface. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when the default generated interface needs extra controls, actions, filters, or layout behavior.

### Reference
`unsetToolbar(string|array $methods)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$methods` | `string\|array` | Yes | - | Toolbar method key or list of keys to remove. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`unsetToolbar()` stores UI configuration for the generated interface. The renderer reads that configuration later and places the control in the correct table, toolbar, filter, grid, or form location.

### Basic Usage
```php
$this->unsetToolbar('export, print');

return $this->render('orders');
```

### Advanced Usage
```php
$this->addToolbar('orders/report', phrase('Report'), 'btn btn-outline-primary', 'mdi mdi-chart-bar')
    ->addButton('orders/read', phrase('View'), 'btn btn-sm btn-outline-secondary', 'mdi mdi-eye', ['order_id' => 'order_id']);
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
            ->unsetToolbar('export, print');

        return $this->render('orders');
    }
}
```

### Result
The generated interface includes or changes the configured control without requiring a custom view.

### Notes
* This method is chainable and returns the current controller instance.
* Use `phrase()` for visible labels so the interface remains translatable.
* Action parameters can reference row fields so generated buttons point to the current record.

### Common Mistakes
* Hard-coding labels that should be translated with `phrase()`.
* Forgetting row parameters for actions that need the current record ID.

### Related Methods
* [addButton](./addButton)
* [addDropdown](./addDropdown)
* [addToolbar](./addToolbar)
* [addSubmitButton](./addSubmitButton)
* [setButton](./setButton)
