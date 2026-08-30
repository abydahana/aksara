`setPlaceholder()` adds placeholder text to inputs. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`setPlaceholder()` adds placeholder text to inputs. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when the generated table, form, or read view is mostly correct but one or more fields need custom behavior.

### Reference
`setPlaceholder(string|array $params = [], ?string $value = null)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$params` | `string|array` | No | `[]` | String value or associative array of values, depending on the method. |
| `$value` | `?string` | No | `null` | Value assigned to the given key or field. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`setPlaceholder()` updates field metadata used by the renderer. The generated output changes when the table, read, or form view is rendered.

### Basic Usage
```php
$this->setPlaceholder('email', phrase('name@example.com'));

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
            ->setPlaceholder('email', phrase('name@example.com'));

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
* [addClass](./addClass)
* [setAttribute](./setAttribute)
* [setTooltip](./setTooltip)
