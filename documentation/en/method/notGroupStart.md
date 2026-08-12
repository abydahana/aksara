Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`notGroupStart($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->notGroupStart('foo', 'bar');`

`$this->notGroupStart('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->notGroupStart([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [groupStart](./groupStart)
* [orGroupStart](./orGroupStart)
* [orNotGroupStart](./orNotGroupStart)
* [groupEnd](./groupEnd)
