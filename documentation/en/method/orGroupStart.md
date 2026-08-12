Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`orGroupStart($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->orGroupStart('foo', 'bar');`

`$this->orGroupStart('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->orGroupStart([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [groupStart](./groupStart)
* [notGroupStart](./notGroupStart)
* [orNotGroupStart](./orNotGroupStart)
* [groupEnd](./groupEnd)
