`render()` dispatches the configured controller into the proper output.

### Purpose
`render()` dispatches the configured controller into the proper output. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when you need to extend the Core CRUD flow while keeping Aksara validation, hooks, formatting, and response handling.

### Reference
`render(?string $table = null, ?string $view = null)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$table` | `?string` | No | `null` | Database table name. |
| `$view` | `?string` | No | `null` | Custom view path. |

### Return Value
`mixed`

Returns the final response for the active request. The concrete value can be a rendered page, JSON response, redirect, generated document, or exception response depending on method and request context.

### Behavior
`render()` runs inside the Core data/rendering pipeline. Depending on the method, it may format data, validate input, execute a CRUD operation, or return an Aksara response.

> [!IMPORTANT]
> `render()` is the point where Core compiles configuration, query, validation, CRUD actions, and output. Put configuration calls before it.

### Basic Usage
```php
return $this->render('orders');
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
    public function index()
    {
        $this->setTitle(phrase('Orders'))
            ->setPrimary('order_id')
            ->where('orders.deleted_at', null);

        return $this->render('orders');
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
* [renderTable](./renderTable)
* [renderRead](./renderRead)
* [renderForm](./renderForm)
* [serialize](./serialize)
* [serializeRow](./serializeRow)
* [validateForm](./validateForm)
