Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`groupStart($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->groupStart('foo', 'bar');`

`$this->groupStart('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->groupStart([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [orGroupStart](./orGroupStart)
* [notGroupStart](./notGroupStart)
* [orNotGroupStart](./orNotGroupStart)
* [groupEnd](./groupEnd)
