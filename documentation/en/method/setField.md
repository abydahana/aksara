`setField()` changes how fields are rendered.

### Purpose
`setField()` changes how fields are rendered. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when the generated table, form, or read view is mostly correct but one or more fields need custom behavior.

### Reference
`setField(string|array $field = [], string|array|null $type = null, array|string|null $parameter = null, mixed $alpha = null, mixed $beta = null, mixed $charlie = null, ?string $delta = null)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$field` | <code>string&#124;array</code> | No | `[]` | Field name, field list, or associative field configuration. |
| `$type` | <code>string&#124;array&#124;null</code> | No | `null` | Field renderer type or mock field type. |
| `$parameter` | <code>array&#124;string&#124;null</code> | No | `null` | Route or query parameters passed to the action. |
| `$alpha` | `mixed` | No | `null` | Extra field parameter consumed by specific field renderers. |
| `$beta` | `mixed` | No | `null` | Extra field parameter consumed by specific field renderers. |
| `$charlie` | `mixed` | No | `null` | Extra field parameter consumed by specific field renderers. |
| `$delta` | `?string` | No | `null` | Extra field parameter consumed by specific field renderers. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`setField()` updates field metadata used by the renderer. The generated output changes when the table, read, or form view is rendered.

### Basic Usage
```php
$this->setField('description', 'textarea');

return $this->render('orders');
```

### Advanced Usage
```php
$this->setAlias(['created_at' => phrase('Created'), 'updated_at' => phrase('Updated')])
    ->setValidation(['title' => 'required|max_length[160]', 'status' => 'required'])
    ->fieldOrder('title, slug, status, created_at');
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
            ->setField('description', 'textarea');

        return $this->render('orders');
    }
}
```

### Result
The generated table, form, or read view uses the configured field behavior when the response is prepared.

### Notes
* This method is chainable and returns the current controller instance.
* Field names must match table columns, selected aliases, relation aliases, or mock fields.
* Most field configuration methods accept a single field/value pair or an associative array for bulk configuration.

### Common Mistakes
* Using a field name that is not present in the selected data.
* Expecting the method to output HTML immediately instead of configuring the renderer.

### Related Methods
* [addField](./addField)
* [verticalSchema](./verticalSchema)
* [setRelation](./setRelation)
* [setAutocomplete](./setAutocomplete)
* [setValidation](./setValidation)
