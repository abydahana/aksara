Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`orHaving($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->orHaving('foo', 'bar');`

`$this->orHaving('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->orHaving([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [having](./having)
* [orHavingIn](./orHavingIn)
* [orHavingNotIn](./orHavingNotIn)
* [havingIn](./havingIn)
* [havingNotIn](./havingNotIn)
* [havingLike](./havingLike)
* [orHavingLike](./orHavingLike)
* [notHavingLike](./notHavingLike)
* [orNotHavingLike](./orNotHavingLike)
