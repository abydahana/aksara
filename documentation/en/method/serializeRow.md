`serializeRow()` normalizes a single row before rendering.

### Purpose
`serializeRow()` normalizes a single row before rendering. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when you need to extend the Core CRUD flow while keeping Aksara validation, hooks, formatting, and response handling.

### Reference
`serializeRow(array|object $data, ?array $fieldData = null, ?array $mockFields = null, ?array $fieldNames = null)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$data` | <code>array&#124;object</code> | Yes | - | Data row, rows, or submitted values. |
| `$fieldData` | `?array` | No | `null` | Compiled field metadata. |
| `$mockFields` | `?array` | No | `null` | Mock field metadata. |
| `$fieldNames` | `?array` | No | `null` | Compiled field-name list. |

### Return Value
`array`

Returns one normalized row where each field contains primary, value, content, maxlength, hidden, type, and validation metadata.

### Behavior
`serializeRow()` runs inside the Core data/rendering pipeline. Depending on the method, it may format data, validate input, execute a CRUD operation, or return an Aksara response.

### Basic Usage
```php
$order = $this->model->getWhere('orders', ['order_id' => 10], 1)->row();
$row = $this->serializeRow($order);
```

### Advanced Usage
```php
$order = $this->model->getWhere('orders', ['order_id' => 10], 1)->row();
$row = $this->serializeRow($order);

return $this->setOutput('preview', $row)->render();
```

### Complete Example
```php
namespace Modules\Orders\Controllers;

use Aksara\Laboratory\Core;

class Orders extends Core
{
    public function preview()
    {
        $order = $this->model->getWhere('orders', ['order_id' => 10], 1)->row();
        $row = $this->serializeRow($order);

        return $this->setOutput('preview', $row)->render();
    }
}
```

### Result
The Core pipeline returns the documented value while preserving Aksara validation, hook, permission, audit, and response behavior where applicable.

### Notes
* These methods are usually called by `render()` internally, but can be useful for advanced modules.
* Keep direct calls close to the surrounding CRUD logic so the response flow is easy to audit.

### Common Mistakes
* Bypassing Core validation or hooks unintentionally by writing directly to the database.
* Returning raw arrays when the caller expects an Aksara response object.

### Related Methods
* [render](./render)
* [renderTable](./renderTable)
* [renderRead](./renderRead)
* [renderForm](./renderForm)
* [serialize](./serialize)
* [validateForm](./validateForm)
