Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`selectCount($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->selectCount('foo', 'bar');`

`$this->selectCount('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->selectCount([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [distinct](./distinct)
* [select](./select)
* [selectAvg](./selectAvg)
* [selectMax](./selectMax)
* [selectMin](./selectMin)
* [selectSubquery](./selectSubquery)
* [selectSum](./selectSum)
