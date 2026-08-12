Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`unsetRead($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->unsetRead('foo', 'bar');`

`$this->unsetRead('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->unsetRead([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [unsetDelete](./unsetDelete)
* [unsetUpdate](./unsetUpdate)
