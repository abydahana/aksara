Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`orNotHavingGroupStart($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->orNotHavingGroupStart('foo', 'bar');`

`$this->orNotHavingGroupStart('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->orNotHavingGroupStart([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [havingGroupStart](./havingGroupStart)
* [orHavingGroupStart](./orHavingGroupStart)
* [notHavingGroupStart](./notHavingGroupStart)
* [havingGroupEnd](./havingGroupEnd)
