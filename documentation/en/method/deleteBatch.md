`deleteBatch()` deletes posted rows in a batch operation. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`deleteBatch()` deletes posted rows in a batch operation. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when you need to extend the Core CRUD flow while keeping Aksara validation, hooks, formatting, and response handling.

### Reference
`deleteBatch(?string $table = null)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$table` | `?string` | No | `null` | Database table name. |

### Return Value
`object|null`

Returns an Aksara response object describing the batch delete result, or `null` when the flow has no explicit response to return.

### Behavior
`deleteBatch()` runs inside the Core data/rendering pipeline. Depending on the method, it may format data, validate input, execute a CRUD operation, or return an Aksara response.

### Basic Usage
```php
$result = $this->deleteBatch('orders');
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
        $result = $this->deleteBatch('orders');

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
* [deleteData](./deleteData)
* [insertId](./insertId)
* [beforeInsert](./beforeInsert)
* [afterInsert](./afterInsert)
* [beforeUpdate](./beforeUpdate)
* [afterUpdate](./afterUpdate)
