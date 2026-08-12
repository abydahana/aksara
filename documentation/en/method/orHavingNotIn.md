Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`orHavingNotIn($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->orHavingNotIn('foo', 'bar');`

`$this->orHavingNotIn('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->orHavingNotIn([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [having](./having)
* [orHaving](./orHaving)
* [orHavingIn](./orHavingIn)
* [havingIn](./havingIn)
* [havingNotIn](./havingNotIn)
* [havingLike](./havingLike)
* [orHavingLike](./orHavingLike)
* [notHavingLike](./notHavingLike)
* [orNotHavingLike](./orNotHavingLike)
