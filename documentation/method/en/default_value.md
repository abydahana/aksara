Metode ini digunakan apabila ingin memberikan nilai default pada suatu kolom input pada formulir "Tambah", namun tetap dapat diubah oleh pengguna aplikasi.

### Reference
`default_value($field, $value)`

**Parameter**
* **$field** [`mixed`] *nama bidang (field) yang akan diberikan nilai;*
* **$value** [`mixed`] *nilai yang akan digunakan.*

&nbsp;

### Usage Sample
`$this->default_value('foo', 'Bar');`

`$this->default_value('baz', 'Qux');`

Parameter di atas akan menghasilkan sebuah bidang input seperti berikut:
`<input type="text" name="foo" value="Bar" />`

`<input type="text" name="baz" value="Qux" />`

**Anda juga dapat menjalankan metode secara berkelompok seperti berikut:**
```php
$this->default_value([
    'foo' => 'Bar',
    'baz' => 'Qux'
]);
```

&nbsp;

### Read Also
* [set_default](./set_default)
