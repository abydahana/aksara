Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`orNotGroupStart($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->orNotGroupStart('foo', 'bar');`

`$this->orNotGroupStart('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->orNotGroupStart([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [groupStart](./groupStart)
* [orGroupStart](./orGroupStart)
* [notGroupStart](./notGroupStart)
* [groupEnd](./groupEnd)
