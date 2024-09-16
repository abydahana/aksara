Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`unset_delete($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->unset_delete('foo', 'bar');`

`$this->unset_delete('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->unset_delete([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [unset_read](./unset_read)
* [unset_update](./unset_update)
