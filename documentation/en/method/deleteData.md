`deleteData()` deletes one row through the Core CRUD pipeline. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`deleteData()` deletes one row through the Core CRUD pipeline. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when you need to extend the Core CRUD flow while keeping Aksara validation, hooks, formatting, and response handling.

### Reference
`deleteData(?string $table = null, array $where = [], int $limit = 1)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$table` | `?string` | No | `null` | Database table name. |
| `$where` | `array` | No | `[]` | Additional conditions. |
| `$limit` | `int` | No | `1` | Maximum number of rows. |

### Return Value
`object|null`

Returns an Aksara response object on success or failure, or `null` when the flow has no explicit response to return.

### Behavior
`deleteData()` runs inside the Core data/rendering pipeline. Depending on the method, it may format data, validate input, execute a CRUD operation, or return an Aksara response.

### Basic Usage
```php
$result = $this->deleteData('orders', ['order_id' => 10]);
```

### Advanced Usage
```php
$this->setValidation('status', 'required|in_list[draft,paid,cancelled]');

return $this->render('orders');
```

### Complete Example
```php
namespace Modules\Orders\Controllers;

use Aksara\Controllers\BaseController;

class Orders extends BaseController
{
    public function process()
    {
        $result = $this->deleteData('orders', ['order_id' => 10]);

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
* [updateData](./updateData)
* [deleteBatch](./deleteBatch)
* [insertId](./insertId)
* [beforeInsert](./beforeInsert)
* [afterInsert](./afterInsert)
* [beforeUpdate](./beforeUpdate)
* [afterUpdate](./afterUpdate)
