`gridView()` switches the index table into a grid layout. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`gridView()` switches the index table into a grid layout. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when the default generated interface needs extra controls, actions, filters, or layout behavior.

### Reference
`gridView(string $thumbnail, ?string $hyperlink = null, array $parameter = [], bool $newTab = false)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$thumbnail` | `string` | Yes | - | Image or file field used as the grid thumbnail. |
| `$hyperlink` | `?string` | No | `null` | Optional route used when a grid item is clicked. |
| `$parameter` | `array` | No | `[]` | Route or query parameters passed to the action. |
| `$newTab` | `bool` | No | `false` | Open target in a new browser tab when true. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`gridView()` stores UI configuration for the generated interface. The renderer reads that configuration later and places the control in the correct table, toolbar, filter, grid, or form location.

### Basic Usage
```php
$this->gridView('photo', 'galleries/read', ['gallery_id' => 'gallery_id']);

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
            ->gridView('photo', 'galleries/read', ['gallery_id' => 'gallery_id']);

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
* [addFilter](./addFilter)
* [searchable](./searchable)
* [sortable](./sortable)
