Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`notHavingLike($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->notHavingLike('foo', 'bar');`

`$this->notHavingLike('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->notHavingLike([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [having](./having)
* [orHaving](./orHaving)
* [orHavingIn](./orHavingIn)
* [orHavingNotIn](./orHavingNotIn)
* [havingIn](./havingIn)
* [havingNotIn](./havingNotIn)
* [havingLike](./havingLike)
* [orHavingLike](./orHavingLike)
* [orNotHavingLike](./orNotHavingLike)
