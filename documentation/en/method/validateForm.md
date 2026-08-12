Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`validateForm($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->validateForm('foo', 'bar');`

`$this->validateForm('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->validateForm([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [formCallback](./formCallback)
* [validToken](./validToken)
