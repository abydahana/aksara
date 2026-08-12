Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`notLike($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->notLike('foo', 'bar');`

`$this->notLike('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->notLike([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [like](./like)
* [orLike](./orLike)
* [orNotLike](./orNotLike)
