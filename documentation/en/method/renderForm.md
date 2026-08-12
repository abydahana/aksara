Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`renderForm($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->renderForm('foo', 'bar');`

`$this->renderForm('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->renderForm([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [fieldPrepend](./fieldPrepend)
