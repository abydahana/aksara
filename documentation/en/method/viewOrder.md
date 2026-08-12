Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`viewOrder($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->viewOrder('foo', 'bar');`

`$this->viewOrder('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->viewOrder([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [columnOrder](./columnOrder)
* [fieldOrder](./fieldOrder)
