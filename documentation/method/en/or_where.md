Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`or_where($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->or_where('foo', 'bar');`

`$this->or_where('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->or_where([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [where](./where)
* [or_where_in](./or_where_in)
* [or_where_not_in](./or_where_not_in)
* [where_in](./where_in)
* [where_not_in](./where_not_in)
