Your contribution's needed!
Please update this page through GitHub using this standard format.

### Reference
`merge_content($foo, $bar)`

**Parameter**
* **$foo** [`string`] *the detail related to the variable;*
* **$bar** [`string`] *the detail related to the variable.*

&nbsp;

### Usage Sample
`$this->merge_content('foo', 'bar');`

`$this->merge_content('baz', 'qux');`

**You can use this method in groups as below:**
```php
$this->merge_content([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [merge_field](./merge_field)
