Kontribusi kalian dibutuhkan!
Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

### Referensi
`item_reference($foo, $bar)`

**Parameter**
* **$foo** [`string`] *keterangan terkait variabel;*
* **$bar** [`string`] *keterangan terkait variabel.*

&nbsp;

### Contoh Penggunaan
`$this->item_reference('foo', 'bar');`

`$this->item_reference('baz', 'qux');`

**Anda juga dapat menggunakan metode ini secara berkelompok seperti berikut:**
```php
$this->item_reference([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Baca Juga
* [render](./render)
