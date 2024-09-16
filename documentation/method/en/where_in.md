Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`where_in($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->where_in('foo', 'bar');`

`$this->where_in('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->where_in([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [where](./where)
* [or_where](./or_where)
* [or_where_in](./or_where_in)
* [or_where_not_in](./or_where_not_in)
* [where_not_in](./where_not_in)
