`setAutocomplete()` turns a field into an autocomplete relation input.

### Purpose
`setAutocomplete()` turns a field into an autocomplete relation input. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when the generated table, form, or read view is mostly correct but one or more fields need custom behavior.

### Reference
`setAutocomplete(string $field, string $selectedValue, array $output, array $where = [], array $join = [], array $orderBy = [], ?string $groupBy = null, int $limit = 0)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$field` | `string` | Yes | - | Field name, field list, or associative field configuration. |
| `$selectedValue` | `string` | Yes | - | Related table key selected by autocomplete. |
| `$output` | `array` | Yes | - | Magic-string output template or autocomplete output map. |
| `$where` | `array` | No | `[]` | Additional conditions. |
| `$join` | `array` | No | `[]` | Additional joins. |
| `$orderBy` | `array` | No | `[]` | Ordering rules. |
| `$groupBy` | `?string` | No | `null` | Grouping rule. |
| `$limit` | `int` | No | `0` | Maximum number of rows. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`setAutocomplete()` updates field metadata used by the renderer. The generated output changes when the table, read, or form view is rendered.

> [!NOTE]
>
> Autocomplete depends on relation/search metadata. Make sure the target field can be selected and filtered by the generated query.

### Basic Usage
```php
$this->setAutocomplete('customer_id', 'app_customers.customer_id', ['value' => '{{ app_customers.customer_id }}', 'label' => '{{ app_customers.customer_name }}']);

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
            ->setAutocomplete('customer_id', 'app_customers.customer_id', ['value' => '{{ app_customers.customer_id }}', 'label' => '{{ app_customers.customer_name }}']);

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
* [verticalSchema](./verticalSchema)
* [setRelation](./setRelation)
* [setValidation](./setValidation)
