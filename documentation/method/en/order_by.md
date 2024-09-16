Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`order_by($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->order_by('foo', 'bar');`

`$this->order_by('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->order_by([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [field_prepend](./field_prepend)
