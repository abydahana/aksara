Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`havingGroupStart($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->havingGroupStart('foo', 'bar');`

`$this->havingGroupStart('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->havingGroupStart([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [orHavingGroupStart](./orHavingGroupStart)
* [notHavingGroupStart](./notHavingGroupStart)
* [orNotHavingGroupStart](./orNotHavingGroupStart)
* [havingGroupEnd](./havingGroupEnd)
