Your contribution's needed!

Please update this page through GitHub using this standard format.

### Reference
`selectSubquery($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->selectSubquery('foo', 'bar');`

`$this->selectSubquery('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->selectSubquery([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [distinct](./distinct)
* [select](./select)
* [selectAvg](./selectAvg)
* [selectCount](./selectCount)
* [selectMax](./selectMax)
* [selectMin](./selectMin)
* [selectSum](./selectSum)
