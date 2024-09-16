Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`or_not_like($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->or_not_like('foo', 'bar');`

`$this->or_not_like('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->or_not_like([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [like](./like)
* [or_like](./or_like)
* [not_like](./not_like)
