Kontribusi kalian dibutuhkan!

Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

### Referensi
`or_not_group_start($foo, $bar)`

**Parameter**
* **$foo** [`string`] *keterangan terkait variabel;*
* **$bar** [`string`] *keterangan terkait variabel.*

&nbsp;

### Contoh Penggunaan
`$this->or_not_group_start('foo', 'bar');`

`$this->or_not_group_start('baz', 'qux');`

**Anda juga dapat menggunakan metode ini secara berkelompok seperti berikut:**
```php
$this->or_not_group_start([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Baca Juga
* [group_start](./group_start)
* [or_group_start](./or_group_start)
* [not_group_start](./not_group_start)
* [group_end](./group_end)
