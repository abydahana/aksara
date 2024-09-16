Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`having_in($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->having_in('foo', 'bar');`

`$this->having_in('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->having_in([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [having](./having)
* [or_having](./or_having)
* [or_having_in](./or_having_in)
* [or_having_not_in](./or_having_not_in)
* [having_not_in](./having_not_in)
* [having_like](./having_like)
* [or_having_like](./or_having_like)
* [not_having_like](./not_having_like)
* [or_not_having_like](./or_not_having_like)
