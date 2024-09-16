Kontribusi kalian dibutuhkan!
Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

### Referensi
`like($foo, $bar)`

**Parameter**
* **$foo** [`string`] *keterangan terkait variabel;*
* **$bar** [`string`] *keterangan terkait variabel.*

&nbsp;

### Contoh Penggunaan
`$this->like('foo', 'bar');`

`$this->like('baz', 'qux');`

**Anda juga dapat menggunakan metode ini secara berkelompok seperti berikut:**
```php
$this->like([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Baca Juga
* [or_like](./or_like)
* [not_like](./not_like)
* [or_not_like](./or_not_like)
