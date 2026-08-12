Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`setDefault($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->setDefault('foo', 'bar');`

`$this->setDefault('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->setDefault([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [defaultValue](./defaultValue)
