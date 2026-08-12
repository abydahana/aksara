Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`whereNotIn($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->whereNotIn('foo', 'bar');`

`$this->whereNotIn('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->whereNotIn([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [where](./where)
* [orWhere](./orWhere)
* [orWhereIn](./orWhereIn)
* [orWhereNotIn](./orWhereNotIn)
* [whereIn](./whereIn)
