Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`group_end($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->group_end('foo', 'bar');`

`$this->group_end('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->group_end([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [group_start](./group_start)
* [or_group_start](./or_group_start)
* [not_group_start](./not_group_start)
* [or_not_group_start](./or_not_group_start)
