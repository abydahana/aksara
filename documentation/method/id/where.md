Kontribusi kalian dibutuhkan!
Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

### Referensi
`where($foo, $bar)`

**Parameter**
* **$foo** [`string`] *keterangan terkait variabel;*
* **$bar** [`string`] *keterangan terkait variabel.*

&nbsp;

### Contoh Penggunaan
`$this->where('foo', 'bar');`

`$this->where('baz', 'qux');`

**Anda juga dapat menggunakan metode ini secara berkelompok seperti berikut:**
```php
$this->where([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Baca Juga
* [or_where](./or_where)
* [or_where_in](./or_where_in)
* [or_where_not_in](./or_where_not_in)
* [where_in](./where_in)
* [where_not_in](./where_not_in)
