Sesuai dengan nama metodenya, field append menambahkan komponen atau suffix setelah field yang mana dapat berupa suffix keterangan dari sebuah elemen input.

### Reference
`field_append($field, $append)`

**Parameter**
* **$field** [`mixed`] *nama daripada field;*
* **$append** [`string`] *isi suffix yang digunakan.*

&nbsp;

### Usage Sample
`$this->field_append('foo', 'bar');`

`$this->field_append('baz', 'qux');`

**Anda juga dapat menggunakan metode secara berkelompok seperti berikut:**

```php
$this->field_append([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Read Also
* [field_prepend](./field_prepend)
