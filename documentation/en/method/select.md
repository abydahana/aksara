`select()` adds columns to the SELECT list.

### Purpose
`select()` adds columns to the SELECT list. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when a controller needs to shape the generated dataset before calling `render()` without creating a separate custom query flow.

### Reference
`select(string|array $column, bool $escape = true)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$column` | <code>string&#124;array</code> | Yes | - | Column name or column list. |
| `$escape` | `bool` | No | `true` | Whether the database layer should escape identifiers and values. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`select()` records a query instruction on the controller. Core applies that instruction when `render()` compiles the final query. It does not execute a database query by itself.

> [!WARNING]
> Keep `$escape` enabled unless the selected expression is trusted raw SQL.

### Basic Usage
```php
$this->select('orders.order_id, orders.order_number, customers.customer_name');

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
            ->select('orders.order_id, orders.order_number, customers.customer_name');

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
* [selectCount](./selectCount)
* [selectSum](./selectSum)
* [selectMin](./selectMin)
* [selectMax](./selectMax)
* [selectAvg](./selectAvg)
* [selectSubquery](./selectSubquery)
* [unsetSelect](./unsetSelect)
* [distinct](./distinct)
