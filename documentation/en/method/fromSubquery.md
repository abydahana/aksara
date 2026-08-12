Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`fromSubquery($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->fromSubquery('foo', 'bar');`

`$this->fromSubquery('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->fromSubquery([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [from](./from)
* [table](./table)
* [selectSubquery](./selectSubquery)
