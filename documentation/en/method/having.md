Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`having($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->having('foo', 'bar');`

`$this->having('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->having([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [orHaving](./orHaving)
* [orHavingIn](./orHavingIn)
* [orHavingNotIn](./orHavingNotIn)
* [havingIn](./havingIn)
* [havingNotIn](./havingNotIn)
* [havingLike](./havingLike)
* [orHavingLike](./orHavingLike)
* [notHavingLike](./notHavingLike)
* [orNotHavingLike](./orNotHavingLike)
