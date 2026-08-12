Kontribusi kalian dibutuhkan!

Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

### Referensi
`havingGroupEnd($foo, $bar)`

**Parameter**
* **$foo** [`string`] *keterangan terkait variabel;*
* **$bar** [`string`] *keterangan terkait variabel.*

&nbsp;

### Contoh Penggunaan
`$this->havingGroupEnd('foo', 'bar');`

`$this->havingGroupEnd('baz', 'qux');`

**Anda juga dapat menggunakan metode ini secara berkelompok seperti berikut:**
```php
$this->havingGroupEnd([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Baca Juga
* [havingGroupStart](./havingGroupStart)
* [orHavingGroupStart](./orHavingGroupStart)
* [notHavingGroupStart](./notHavingGroupStart)
* [orNotHavingGroupStart](./orNotHavingGroupStart)
