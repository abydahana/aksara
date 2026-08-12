Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`unsetDelete($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->unsetDelete('foo', 'bar');`

`$this->unsetDelete('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->unsetDelete([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [unsetRead](./unsetRead)
* [unsetUpdate](./unsetUpdate)
