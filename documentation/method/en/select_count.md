Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`select_count($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->select_count('foo', 'bar');`

`$this->select_count('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->select_count([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [distinct](./distinct)
* [select](./select)
* [select_avg](./select_avg)
* [select_max](./select_max)
* [select_min](./select_min)
* [select_subquery](./select_subquery)
* [select_sum](./select_sum)
