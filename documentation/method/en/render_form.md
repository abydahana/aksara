Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`render_form($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->render_form('foo', 'bar');`

`$this->render_form('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->render_form([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [field_prepend](./field_prepend)
