`afterUpdate()` runs custom logic after an update operation. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`afterUpdate()` runs custom logic after an update operation. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Override this method when a module needs custom side effects or checks around create, update, or delete operations.

### Reference
`afterUpdate()`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| None | - | - | - | This method does not accept parameters. |

### Return Value
`mixed|void`

Hooks do not need to return anything. Return an Aksara response or exception object only when the CRUD operation must be stopped.

### Behavior
`afterUpdate()` is an override hook. Core calls it automatically at the matching point in the create, update, or delete flow when the method exists on the controller.

### Basic Usage
```php
protected function afterUpdate()
{
    log_message('info', 'afterUpdate hook executed.');
}
```

### Advanced Usage
```php
protected function afterUpdate()
{
    service('cache')->delete('orders_summary');
}
```

### Complete Example
```php
namespace Modules\Orders\Controllers;

use Aksara\Controllers\BaseController;

class Orders extends BaseController
{
    protected function afterUpdate()
    {
        service('cache')->delete('orders_summary');
    }

    public function index()
    {
        return $this->render('orders');
    }
}
```

### Result
The custom hook logic runs automatically during the matching CRUD operation. If the hook returns a blocking response, the operation can be stopped.

### Notes
* Hooks are optional; define them only when the module needs extra behavior.
* A hook may return an Aksara error/response object when the operation must stop.

### Common Mistakes
* Making the hook public when it is intended to be a protected controller method.
* Performing slow external work synchronously without considering request timeout.

### Related Methods
* [insertData](./insertData)
* [updateData](./updateData)
* [deleteData](./deleteData)
* [deleteBatch](./deleteBatch)
* [insertId](./insertId)
* [beforeInsert](./beforeInsert)
* [afterInsert](./afterInsert)
* [beforeUpdate](./beforeUpdate)
