Kontribusi kalian dibutuhkan!

Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

### Referensi
`havingGroupStart($foo, $bar)`

**Parameter**
* **$foo** [`string`] *keterangan terkait variabel;*
* **$bar** [`string`] *keterangan terkait variabel.*

&nbsp;

### Contoh Penggunaan
`$this->havingGroupStart('foo', 'bar');`

`$this->havingGroupStart('baz', 'qux');`

**Anda juga dapat menggunakan metode ini secara berkelompok seperti berikut:**
```php
$this->havingGroupStart([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Baca Juga
* [orHavingGroupStart](./orHavingGroupStart)
* [notHavingGroupStart](./notHavingGroupStart)
* [orNotHavingGroupStart](./orNotHavingGroupStart)
* [havingGroupEnd](./havingGroupEnd)
