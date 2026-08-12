Kontribusi kalian dibutuhkan!

Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

### Referensi
`distinct($foo, $bar)`

**Parameter**
* **$foo** [`string`] *keterangan terkait variabel;*
* **$bar** [`string`] *keterangan terkait variabel.*

&nbsp;

### Contoh Penggunaan
`$this->distinct('foo', 'bar');`

`$this->distinct('baz', 'qux');`

**Anda juga dapat menggunakan metode ini secara berkelompok seperti berikut:**
```php
$this->distinct([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Baca Juga
* [select](./select)
* [selectAvg](./selectAvg)
* [selectCount](./selectCount)
* [selectMax](./selectMax)
* [selectMin](./selectMin)
* [selectSubquery](./selectSubquery)
* [selectSum](./selectSum)
