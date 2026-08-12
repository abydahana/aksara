Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`havingGroupEnd($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->havingGroupEnd('foo', 'bar');`

`$this->havingGroupEnd('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->havingGroupEnd([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [havingGroupStart](./havingGroupStart)
* [orHavingGroupStart](./orHavingGroupStart)
* [notHavingGroupStart](./notHavingGroupStart)
* [orNotHavingGroupStart](./orNotHavingGroupStart)
