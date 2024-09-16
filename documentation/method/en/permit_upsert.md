Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`permit_upsert($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->permit_upsert('foo', 'bar');`

`$this->permit_upsert('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->permit_upsert([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [render](./render)
