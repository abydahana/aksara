Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`unset_select($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->unset_select('foo', 'bar');`

`$this->unset_select('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->unset_select([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [render](./render)
