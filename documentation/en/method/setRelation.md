Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`setRelation($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->setRelation('foo', 'bar');`

`$this->setRelation('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->setRelation([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [join](./join)
* [setAutocomplete](./setAutocomplete)
