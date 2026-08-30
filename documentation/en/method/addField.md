`addField()` adds a mock field that is not physically present in the table.

### Purpose
`addField()` adds a mock field that is not physically present in the table. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when the generated table, form, or read view is mostly correct but one or more fields need custom behavior.

### Reference
`addField(string|array $name, string $type = 'varchar')`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$name` | <code>string&#124;array</code> | Yes | - | Mock field name or an associative array of `field => type` pairs. |
| `$type` | `string` | No | `'varchar'` | Field renderer type or mock field type. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`addField()` updates field metadata used by the renderer. The generated output changes when the table, read, or form view is rendered.

> [!NOTE]
> Mock fields are useful for display or vertical schema data, but they are not physical columns unless the model or schema provides storage for them.

### Basic Usage
```php
$this->addField('display_name', 'varchar');

return $this->render('orders');
```

### Advanced Usage
```php
$this->addField([
    'display_name' => 'varchar',
    'display_summary' => 'textarea'
])
    ->setAlias('display_name', phrase('Display Name'))
    ->setField('display_summary', 'textarea')
    ->fieldOrder('display_name, display_summary, status');

return $this->render('orders');
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
            ->addField('display_name', 'varchar');

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
* [verticalSchema](./verticalSchema)
* [setRelation](./setRelation)
* [setAutocomplete](./setAutocomplete)
* [setValidation](./setValidation)
