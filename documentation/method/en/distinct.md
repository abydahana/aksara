Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`distinct($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->distinct('foo', 'bar');`

`$this->distinct('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->distinct([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [select](./select)
* [select_avg](./select_avg)
* [select_count](./select_count)
* [select_max](./select_max)
* [select_min](./select_min)
* [select_subquery](./select_subquery)
* [select_sum](./select_sum)
