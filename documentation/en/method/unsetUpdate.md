Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`unsetUpdate($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->unsetUpdate('foo', 'bar');`

`$this->unsetUpdate('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->unsetUpdate([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [unsetDelete](./unsetDelete)
* [unsetRead](./unsetRead)
