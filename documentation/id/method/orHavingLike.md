Kontribusi kalian dibutuhkan!

Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

### Referensi
`orHavingLike($foo, $bar)`

**Parameter**
* **$foo** [`string`] *keterangan terkait variabel;*
* **$bar** [`string`] *keterangan terkait variabel.*

&nbsp;

### Contoh Penggunaan
`$this->orHavingLike('foo', 'bar');`

`$this->orHavingLike('baz', 'qux');`

**Anda juga dapat menggunakan metode ini secara berkelompok seperti berikut:**
```php
$this->orHavingLike([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Baca Juga
* [having](./having)
* [orHaving](./orHaving)
* [orHavingIn](./orHavingIn)
* [orHavingNotIn](./orHavingNotIn)
* [havingIn](./havingIn)
* [havingNotIn](./havingNotIn)
* [havingLike](./havingLike)
* [notHavingLike](./notHavingLike)
* [orNotHavingLike](./orNotHavingLike)
