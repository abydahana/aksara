Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`setBreadcrumb($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->setBreadcrumb('foo', 'bar');`

`$this->setBreadcrumb('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->setBreadcrumb([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [fieldPrepend](./fieldPrepend)
