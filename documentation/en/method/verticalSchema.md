`verticalSchema()` enables vertical key-value schema handling. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`verticalSchema()` enables vertical key-value schema handling. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when the generated table, form, or read view is mostly correct but one or more fields need custom behavior.

### Reference
`verticalSchema(string $keyColumn, string $valueColumn, ?string $typeColumn = 'type')`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$keyColumn` | `string` | Yes | `` | Column that stores the vertical field key. |
| `$valueColumn` | `string` | Yes | `` | Column that stores the vertical field value. |
| `$typeColumn` | `?string` | No | `'type'` | Column that stores the vertical field type. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`verticalSchema()` updates field metadata used by the renderer. The generated output changes when the table, read, or form view is rendered.

### Basic Usage
```php
$this->verticalSchema('setting_key', 'setting_value', 'field_type');

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

use Aksara\Controllers\BaseController;

class Orders extends BaseController
{
    public function index()
    {
        $this->setTitle(phrase('Orders'))
            ->verticalSchema('setting_key', 'setting_value', 'field_type');

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
* [setField](./setField)
* [addField](./addField)
* [setRelation](./setRelation)
* [setAutocomplete](./setAutocomplete)
* [setValidation](./setValidation)
