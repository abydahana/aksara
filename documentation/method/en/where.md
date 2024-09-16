Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`where($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->where('foo', 'bar');`

`$this->where('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->where([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [or_where](./or_where)
* [or_where_in](./or_where_in)
* [or_where_not_in](./or_where_not_in)
* [where_in](./where_in)
* [where_not_in](./where_not_in)
