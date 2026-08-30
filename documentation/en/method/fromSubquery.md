`fromSubquery()` uses a subquery as the source table. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`fromSubquery()` uses a subquery as the source table. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it when a controller needs to shape the generated dataset before calling `render()` without creating a separate custom query flow.

### Reference
`fromSubquery(object|string $subquery, string $alias)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$subquery` | `object|string` | Yes | `` | Prepared subquery object or SQL string. |
| `$alias` | `string` | Yes | `` | Displayed label or SQL alias. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`fromSubquery()` records a query instruction on the controller. Core applies that instruction when `render()` compiles the final query. It does not execute a database query by itself.

### Basic Usage
```php
$activeCustomers = $this->model->builder('customers')
    ->select('customer_id, customer_name')
    ->where('is_active', 1);

$this->fromSubquery($activeCustomers, 'customers');

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

use Aksara\Controllers\BaseController;

class Orders extends BaseController
{
    public function index()
    {
        $activeCustomers = $this->model->builder('customers')
            ->select('customer_id, customer_name')
            ->where('is_active', 1);

        $this->setTitle(phrase('Orders'))
            ->fromSubquery($activeCustomers, 'customers');

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
* [from](./from)
* [table](./table)
* [join](./join)
