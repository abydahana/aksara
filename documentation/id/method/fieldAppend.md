Sesuai dengan nama metodenya, field append menambahkan komponen atau suffix setelah field yang mana dapat berupa suffix keterangan dari sebuah elemen input.

### Referensi
`fieldAppend($field, $append)`

**Parameter**
* **$field** [`mixed`] *nama daripada field;*
* **$append** [`string`] *isi suffix yang digunakan.*

&nbsp;

### Contoh Penggunaan
`$this->fieldAppend('foo', 'bar');`

`$this->fieldAppend('baz', 'qux');`

**Anda juga dapat menggunakan metode secara berkelompok seperti berikut:**

```php
$this->fieldAppend([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Baca Juga
* [fieldPrepend](./fieldPrepend)
