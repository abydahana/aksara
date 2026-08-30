`beforeInsert()` runs custom logic before a create operation.

### Purpose
`beforeInsert()` runs custom logic before a create operation. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Override this method when a module needs custom side effects or checks around create, update, or delete operations.

### Reference
`beforeInsert()`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| None | - | - | - | This method does not accept parameters. |

### Return Value
`mixed|void`

Hooks do not need to return anything. Return an Aksara response or exception object only when the CRUD operation must be stopped.

### Behavior
`beforeInsert()` is an override hook. Core calls it automatically at the matching point in the create, update, or delete flow when the method exists on the controller.

### Basic Usage
```php
protected function beforeInsert()
{
    log_message('info', 'beforeInsert hook executed.');
}
```

### Advanced Usage
```php
protected function beforeInsert()
{
    if (! get_userdata('is_logged')) {
        return throw_exception(403, phrase('Your session has expired.'));
    }
}
```

### Complete Example
```php
namespace Modules\Orders\Controllers;

use Aksara\Laboratory\Core;

class Orders extends Core
{
    protected function beforeInsert()
    {
        if (! get_userdata('is_logged')) {
            return throw_exception(403, phrase('Your session has expired.'));
        }
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
* [afterInsert](./afterInsert)
* [beforeUpdate](./beforeUpdate)
* [afterUpdate](./afterUpdate)
