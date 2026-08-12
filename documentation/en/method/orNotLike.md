Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`orNotLike($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->orNotLike('foo', 'bar');`

`$this->orNotLike('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->orNotLike([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [like](./like)
* [orLike](./orLike)
* [notLike](./notLike)
