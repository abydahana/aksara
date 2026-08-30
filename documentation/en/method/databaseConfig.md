`databaseConfig()` points the controller to a custom database connection. It is used inside an Aksara controller as part of the Core method API.

### Purpose
`databaseConfig()` points the controller to a custom database connection. It lets a controller customize Aksara Core behavior while keeping the request inside the built-in CRUD, rendering, permission, validation, and response pipeline.

### When to Use
Use it near the beginning of a controller method to configure how Core handles the current request.

### Reference
`databaseConfig(array|string $driver = [], ?string $hostname = null, ?int $port = null, ?string $username = null, ?string $password = null, ?string $database = null)`

### Parameters
| Parameter | Type | Required | Default | Description |
|---|---|---:|---|---|
| `$driver` | `array|string` | No | `[]` | Database driver name or a complete connection configuration array. |
| `$hostname` | `?string` | No | `null` | Database host name or IP address. |
| `$port` | `?int` | No | `null` | Database server port. |
| `$username` | `?string` | No | `null` | Database user name. |
| `$password` | `?string` | No | `null` | Database password. |
| `$database` | `?string` | No | `null` | Database or schema name. |

### Return Value
`static`

Returns the current controller instance so it can be chained with other Core methods.

### Behavior
`databaseConfig()` stores request-level configuration on the controller. Call it before the permission, rendering, or form-processing step that depends on it.

### Basic Usage
```php
$this->databaseConfig(['driver' => 'MySQLi', 'hostname' => '127.0.0.1', 'port' => 3306, 'username' => 'reporting_user', 'password' => 'secret', 'database' => 'reporting']);

return $this->render('orders');
```

### Advanced Usage
```php
$this->setTitle(phrase('Orders'))
    ->setIcon('mdi mdi-cart-outline')
    ->setPermission();
```

### Complete Example
```php
namespace Modules\Orders\Controllers;

use Aksara\Controllers\BaseController;

class Orders extends BaseController
{
    public function index()
    {
        $this->setTitle(phrase('Orders'))
            ->databaseConfig(['driver' => 'MySQLi', 'hostname' => '127.0.0.1', 'port' => 3306, 'username' => 'reporting_user', 'password' => 'secret', 'database' => 'reporting']);

        return $this->render('orders');
    }
}
```

### Result
The controller stores the configuration and applies it later in the current request lifecycle.

### Notes
* This method is chainable and returns the current controller instance.
* Call configuration methods before `setPermission()` or `render()` when those steps depend on the configured value.

### Common Mistakes
* Calling the method after the permission or render step that already needed it.
* Spreading related configuration across distant parts of the controller.

### Related Methods
* [render](./render)
