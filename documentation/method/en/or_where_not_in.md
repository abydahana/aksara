Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`or_where_not_in($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->or_where_not_in('foo', 'bar');`

`$this->or_where_not_in('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->or_where_not_in([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [where](./where)
* [or_where](./or_where)
* [or_where_in](./or_where_in)
* [where_in](./where_in)
* [where_not_in](./where_not_in)
