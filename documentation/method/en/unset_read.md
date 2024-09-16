Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`unset_read($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->unset_read('foo', 'bar');`

`$this->unset_read('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->unset_read([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [unset_delete](./unset_delete)
* [unset_update](./unset_update)
