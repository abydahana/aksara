Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`like($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->like('foo', 'bar');`

`$this->like('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->like([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [or_like](./or_like)
* [not_like](./not_like)
* [or_not_like](./or_not_like)
