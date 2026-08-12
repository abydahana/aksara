Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`orHavingGroupStart($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->orHavingGroupStart('foo', 'bar');`

`$this->orHavingGroupStart('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->orHavingGroupStart([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [havingGroupStart](./havingGroupStart)
* [notHavingGroupStart](./notHavingGroupStart)
* [orNotHavingGroupStart](./orNotHavingGroupStart)
* [havingGroupEnd](./havingGroupEnd)
