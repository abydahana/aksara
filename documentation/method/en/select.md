Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`select($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->select('foo', 'bar');`

`$this->select('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->select([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [distinct](./distinct)
* [select_avg](./select_avg)
* [select_count](./select_count)
* [select_max](./select_max)
* [select_min](./select_min)
* [select_subquery](./select_subquery)
* [select_sum](./select_sum)
