Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`orWhereNotIn($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->orWhereNotIn('foo', 'bar');`

`$this->orWhereNotIn('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->orWhereNotIn([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [where](./where)
* [orWhere](./orWhere)
* [orWhereIn](./orWhereIn)
* [whereIn](./whereIn)
* [whereNotIn](./whereNotIn)
