`selectMin()` adds a MIN aggregate column. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`selectMin()` adds a MIN aggregate column. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when a controller needs to shape the generated dataset before calling `render()` without creating a separate custom query flow.

### Reference
`selectMin(string $column, ?string $alias = null)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$column` | `string` | Yes | - | Column name or column list. |
| `$alias` | `?string` | No | `null` | Displayed label or SQL alias. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`selectMin()` records a query instruction on the controller. Core applies that instruction when `render()` compiles the final query. It does not execute a database query by itself.

### Basic Usage
```php
$this->selectMin('products.price', 'lowest_price');

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
            ->selectMin('products.price', 'lowest_price');

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
* [select](./select)
* [selectCount](./selectCount)
* [selectSum](./selectSum)
* [selectMax](./selectMax)
* [selectAvg](./selectAvg)
* [selectSubquery](./selectSubquery)
* [unsetSelect](./unsetSelect)
* [distinct](./distinct)
