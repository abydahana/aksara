Sesuai dengan nama metodenya, field append menambahkan komponen atau suffix setelah field yang mana dapat berupa suffix keterangan dari sebuah elemen input.

### Referensi
`field_append($field, $append)`

**Parameter**
* **$field** [`mixed`] *nama daripada field;*
* **$append** [`string`] *isi suffix yang digunakan.*

&nbsp;

### Contoh Penggunaan
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

### Baca Juga
* [field_prepend](./field_prepend)
