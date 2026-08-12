Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`mergeField($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->mergeField('foo', 'bar');`

`$this->mergeField('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->mergeField([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [mergeContent](./mergeContent)
