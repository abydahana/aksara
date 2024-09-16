Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`view_order($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->view_order('foo', 'bar');`

`$this->view_order('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->view_order([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [column_order](./column_order)
* [field_order](./field_order)
