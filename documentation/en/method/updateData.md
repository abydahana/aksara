`updateData()` updates rows through the Core CRUD pipeline.

### Purpose
`updateData()` updates rows through the Core CRUD pipeline. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when you need to extend the Core CRUD flow while keeping Aksara validation, hooks, formatting, and response handling.

### Reference
`updateData(?string $table = null, array $data = [], array $where = [])`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$table` | `?string` | No | `null` | Database table name. |
| `$data` | `array` | No | `[]` | Data row, rows, or submitted values. |
| `$where` | `array` | No | `[]` | Additional conditions. |

### Return Value
`object|bool`

Returns an Aksara response object on success or failure, or `false` when the update flow cannot produce a response.

### Behavior
`updateData()` runs inside the Core data/rendering pipeline. Depending on the method, it may format data, validate input, execute a CRUD operation, or return an Aksara response.

> [!WARNING]
>
> Always pass a narrow `$where` condition when calling `updateData()` directly. Empty or broad conditions can update the wrong record set.

### Basic Usage
```php
$result = $this->updateData('orders', ['status' => 'paid'], ['order_id' => 10]);
```

### Advanced Usage
```php
$this->setValidation('status', 'required|in_list[draft,paid,cancelled]');

return $this->render('orders');
```

### Complete Example
```php
namespace Modules\Orders\Controllers;

use Aksara\Laboratory\Core;

class Orders extends Core
{
    public function process()
    {
        $result = $this->updateData('orders', ['status' => 'paid'], ['order_id' => 10]);

        return throw_exception(200, phrase('The request has been processed.'));
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
* [insertData](./insertData)
* [deleteData](./deleteData)
* [deleteBatch](./deleteBatch)
* [insertId](./insertId)
* [beforeInsert](./beforeInsert)
* [afterInsert](./afterInsert)
* [beforeUpdate](./beforeUpdate)
* [afterUpdate](./afterUpdate)
