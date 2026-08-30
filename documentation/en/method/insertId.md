`insertId()` returns the last inserted primary key value.

### Purpose
`insertId()` returns the last inserted primary key value. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when you need to extend the Core CRUD flow while keeping Aksara validation, hooks, formatting, and response handling.

### Reference
`insertId()`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| None | - | - | - | This method does not accept parameters. |

### Return Value
`int`

Returns the integer insert ID captured by the latest successful Core insert operation.

### Behavior
`insertId()` runs inside the Core data/rendering pipeline. Depending on the method, it may format data, validate input, execute a CRUD operation, or return an Aksara response.

### Basic Usage
```php
$orderId = $this->insertId();
```

### Advanced Usage
```php
$this->insertData('orders', ['status' => 'paid']);

$orderId = $this->insertId();
return $this->setOutput('order_id', $orderId)->render();
```

### Complete Example
```php
namespace Modules\Orders\Controllers;

use Aksara\Laboratory\Core;

class Orders extends Core
{
    public function process()
    {
        $this->insertData('orders', ['status' => 'paid']);
        $orderId = $this->insertId();

        return $this->setOutput('order_id', $orderId)->render();
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
* [deleteBatch](./deleteBatch)
* [beforeInsert](./beforeInsert)
* [afterInsert](./afterInsert)
* [beforeUpdate](./beforeUpdate)
* [afterUpdate](./afterUpdate)
