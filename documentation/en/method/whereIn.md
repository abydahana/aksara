Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`whereIn($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->whereIn('foo', 'bar');`

`$this->whereIn('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->whereIn([
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
* [whereNotIn](./whereNotIn)
