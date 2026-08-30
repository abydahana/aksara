`serialize()` normalizes multiple rows before rendering. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`serialize()` normalizes multiple rows before rendering. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when you need to extend the Core CRUD flow while keeping Aksara validation, hooks, formatting, and response handling.

### Reference
`serialize(array $data)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$data` | `array` | Yes | `` | Data row, rows, or submitted values. |

### Return Value
`array`

Returns an array of normalized rows where every field includes value, content, type, hidden state, and validation metadata.

### Behavior
`serialize()` runs inside the Core data/rendering pipeline. Depending on the method, it may format data, validate input, execute a CRUD operation, or return an Aksara response.

### Basic Usage
```php
$rows = $this->model->get('orders')->result();
$serialized = $this->serialize($rows);
```

### Advanced Usage
```php
$rows = $this->model->get('orders')->result();
$serialized = $this->serialize($rows);

return $this->setOutput('preview', $serialized)->render();
```

### Complete Example
```php
namespace Modules\Orders\Controllers;

use Aksara\Controllers\BaseController;

class Orders extends BaseController
{
    public function preview()
    {
        $rows = $this->model->get('orders')->result();
        $serialized = $this->serialize($rows);

        return $this->setOutput('preview', $serialized)->render();
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
* [serializeRow](./serializeRow)
* [validateForm](./validateForm)
