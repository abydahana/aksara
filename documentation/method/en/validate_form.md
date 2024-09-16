Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`validate_form($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->validate_form('foo', 'bar');`

`$this->validate_form('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->validate_form([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [form_callback](./form_callback)
* [valid_token](./valid_token)
