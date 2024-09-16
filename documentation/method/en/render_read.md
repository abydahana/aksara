Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`render_read($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->render_read('foo', 'bar');`

`$this->render_read('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->render_read([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [field_prepend](./field_prepend)
