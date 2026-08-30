`orderBy()` sets default result ordering. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`orderBy()` sets default result ordering. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when a controller needs to shape the generated dataset before calling `render()` without creating a separate custom query flow.

### Reference
`orderBy(string|array $field = [], string $direction = '', bool $escape = true)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$field` | `string\|array` | No | `[]` | Field name, field list, or associative field configuration. |
| `$direction` | `string` | No | `''` | Sort direction such as `ASC` or `DESC`. |
| `$escape` | `bool` | No | `true` | Whether the database layer should escape identifiers and values. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`orderBy()` records a query instruction on the controller. Core applies that instruction when `render()` compiles the final query. It does not execute a database query by itself.

### Basic Usage
```php
$this->orderBy('orders.created_at', 'DESC');

return $this->render('orders');
```

### Advanced Usage
```php
$this->select('orders.order_id, orders.order_number, customers.customer_name')
    ->join('customers', 'customers.customer_id = orders.customer_id', 'left')
    ->where('orders.deleted_at', null)
    ->orderBy('orders.created_at', 'DESC')
    ->limit(25);

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
            ->orderBy('orders.created_at', 'DESC');

        return $this->render('orders');
    }
}
```

### Result
The final generated query includes this instruction before rows are serialized and rendered. API responses use the same prepared query.

### Notes
* This method is chainable and returns the current controller instance.
* Call this before `render()` so the query instruction is available during query compilation.
* Leave `$escape` enabled unless the expression has already been validated and intentionally needs raw SQL behavior.

### Common Mistakes
* Calling the method after `render()`, because the query has already been compiled.
* Disabling escaping for untrusted request input.

### Related Methods
* [groupBy](./groupBy)
* [limit](./limit)
* [offset](./offset)
