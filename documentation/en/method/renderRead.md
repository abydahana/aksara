Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`renderRead($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->renderRead('foo', 'bar');`

`$this->renderRead('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->renderRead([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [fieldPrepend](./fieldPrepend)
