Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`permitUpsert($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->permitUpsert('foo', 'bar');`

`$this->permitUpsert('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->permitUpsert([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [render](./render)
