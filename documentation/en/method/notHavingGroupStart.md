Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`notHavingGroupStart($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->notHavingGroupStart('foo', 'bar');`

`$this->notHavingGroupStart('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->notHavingGroupStart([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [havingGroupStart](./havingGroupStart)
* [orHavingGroupStart](./orHavingGroupStart)
* [orNotHavingGroupStart](./orNotHavingGroupStart)
* [havingGroupEnd](./havingGroupEnd)
