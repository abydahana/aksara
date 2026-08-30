`groupBy()` groups query results.

### Purpose
`groupBy()` groups query results. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when a controller needs to shape the generated dataset before calling `render()` without creating a separate custom query flow.

### Reference
`groupBy(string $column)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$column` | `string` | Yes | - | Column name or column list. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`groupBy()` records a query instruction on the controller. Core applies that instruction when `render()` compiles the final query. It does not execute a database query by itself.

### Basic Usage
```php
$this->groupBy('orders.customer_id');

return $this->render('orders');
```

### Advanced Usage
```php
$this->select('orders.customer_id')
    ->selectSum('orders.amount', 'total_amount')
    ->groupBy('orders.customer_id')
    ->having('total_amount >', 100000)
    ->orderBy('total_amount', 'DESC', false);

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
            ->groupBy('orders.customer_id');

        return $this->render('orders');
    }
}
```

### Result
The final generated query includes this instruction before rows are serialized and rendered. API responses use the same prepared query.

### Notes
* This method is chainable and returns the current controller instance.
* Call this before `render()` so the query instruction is available during query compilation.

### Common Mistakes
* Calling the method after `render()`, because the query has already been compiled.

### Related Methods
* [orderBy](./orderBy)
* [limit](./limit)
* [offset](./offset)
