`setButton()` overrides a built-in CRUD button.

### Purpose
`setButton()` overrides a built-in CRUD button. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when the default generated interface needs extra controls, actions, filters, or layout behavior.

### Reference
`setButton(string $button, ?string $value = null, ?string $label = null, ?string $class = null, ?string $icon = null, array $parameter = [], ?bool $newTab = false)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$button` | `string` | Yes | - | Built-in button key to override. |
| `$value` | `?string` | No | `null` | Value assigned to the given key or field. |
| `$label` | `?string` | No | `null` | Human-readable label. |
| `$class` | `?string` | No | `null` | CSS class list. |
| `$icon` | `?string` | No | `null` | Icon class. |
| `$parameter` | `array` | No | `[]` | Route or query parameters passed to the action. |
| `$newTab` | `?bool` | No | `false` | Open target in a new browser tab when true. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`setButton()` stores UI configuration for the generated interface. The renderer reads that configuration later and places the control in the correct table, toolbar, filter, grid, or form location.

### Basic Usage
```php
$this->setButton('create', current_page('create'), phrase('Add Order'), 'btn btn-primary', 'mdi mdi-plus');

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
            ->setButton('create', current_page('create'), phrase('Add Order'), 'btn btn-primary', 'mdi mdi-plus');

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
* [unsetToolbar](./unsetToolbar)
