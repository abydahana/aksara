Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`valid_token($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->valid_token('foo', 'bar');`

`$this->valid_token('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->valid_token([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [form_callback](./form_callback)
* [validate_form](./validate_form)
