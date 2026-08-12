Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`itemReference($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->itemReference('foo', 'bar');`

`$this->itemReference('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->itemReference([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [render](./render)
