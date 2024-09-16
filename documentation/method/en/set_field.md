Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`set_field($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->set_field('foo', 'bar');`

`$this->set_field('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->set_field([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [field_prepend](./field_prepend)
