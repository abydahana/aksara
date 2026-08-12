Kontribusi kalian dibutuhkan!

Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

### Referensi
`selectCount($foo, $bar)`

**Parameter**
* **$foo** [`string`] *keterangan terkait variabel;*
* **$bar** [`string`] *keterangan terkait variabel.*

&nbsp;

### Contoh Penggunaan
`$this->selectCount('foo', 'bar');`

`$this->selectCount('baz', 'qux');`

**Anda juga dapat menggunakan metode ini secara berkelompok seperti berikut:**
```php
$this->selectCount([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Baca Juga
* [distinct](./distinct)
* [select](./select)
* [selectAvg](./selectAvg)
* [selectMax](./selectMax)
* [selectMin](./selectMin)
* [selectSubquery](./selectSubquery)
* [selectSum](./selectSum)
