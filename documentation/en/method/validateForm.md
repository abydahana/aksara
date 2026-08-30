`validateForm()` validates submitted create/update data.

### Purpose
`validateForm()` validates submitted create/update data. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when you need to extend the Core CRUD flow while keeping Aksara validation, hooks, formatting, and response handling.

### Reference
`validateForm(array|object $data)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$data` | <code>array&#124;object</code> | Yes | - | Data row, rows, or submitted values. |

### Return Value
`mixed`

Returns an Aksara response object when validation fails or when the prepared create/update flow completes. It may return no explicit value when Core continues internally.

### Behavior
`validateForm()` runs inside the Core data/rendering pipeline. Depending on the method, it may format data, validate input, execute a CRUD operation, or return an Aksara response.

### Basic Usage
```php
$order = $this->model->getWhere('orders', ['order_id' => 10], 1)->row();
$validation = $this->validateForm($order);
```

### Advanced Usage
```php
$order = $this->model->getWhere('orders', ['order_id' => 10], 1)->row();
$this->setValidation('status', 'required|in_list[draft,paid,cancelled]');

return $this->validateForm($order);
```

### Complete Example
```php
namespace Modules\Orders\Controllers;

use Aksara\Laboratory\Core;

class Orders extends Core
{
    public function process()
    {
        $order = $this->model->getWhere('orders', ['order_id' => 10], 1)->row();
        $this->setValidation('status', 'required|in_list[draft,paid,cancelled]');

        return $this->validateForm($order);
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
* [serializeRow](./serializeRow)
