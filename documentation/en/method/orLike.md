Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`orLike($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->orLike('foo', 'bar');`

`$this->orLike('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->orLike([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [like](./like)
* [notLike](./notLike)
* [orNotLike](./orNotLike)
