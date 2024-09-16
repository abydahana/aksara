Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`having_group_start($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->having_group_start('foo', 'bar');`

`$this->having_group_start('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->having_group_start([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [or_having_group_start](./or_having_group_start)
* [not_having_group_start](./not_having_group_start)
* [or_not_having_group_start](./or_not_having_group_start)
* [having_group_end](./having_group_end)
