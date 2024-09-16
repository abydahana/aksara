Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`item_reference($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->item_reference('foo', 'bar');`

`$this->item_reference('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->item_reference([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [render](./render)
