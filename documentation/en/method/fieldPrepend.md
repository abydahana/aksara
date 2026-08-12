Sesuai dengan nama metodenya, field prepend menambahkan komponen atau prefix pada field yang mana dapat berupa prefix keterangan dari sebuah elemen input.

### Reference
`fieldPrepend($field, $prepend)`

**Parameter**
* **$field** [mixed] *nama daripada field;*
* **$prepend** [string] *isi prefix yang digunakan.*

&nbsp;

### Usage Sample
`$this->fieldPrepend('foo', 'bar');`

`$this->fieldPrepend('baz', 'qux');`

**Anda juga dapat menggunakan metode secara berkelompok seperti berikut:**
```php
$this->fieldPrepend([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [fieldAppend](./fieldAppend)
