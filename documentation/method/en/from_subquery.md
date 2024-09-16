Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`from_subquery($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->from_subquery('foo', 'bar');`

`$this->from_subquery('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->from_subquery([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [from](./from)
* [table](./table)
* [select_subquery](./select_subquery)
