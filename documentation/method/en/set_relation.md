Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`set_relation($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->set_relation('foo', 'bar');`

`$this->set_relation('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->set_relation([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [join](./join)
* [set_autocomplete](./set_autocomplete)
