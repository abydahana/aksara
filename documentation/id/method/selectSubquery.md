Kontribusi kalian dibutuhkan!

Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

### Referensi
`selectSubquery($foo, $bar)`

**Parameter**
* **$foo** [`string`] *keterangan terkait variabel;*
* **$bar** [`string`] *keterangan terkait variabel.*

&nbsp;

### Contoh Penggunaan
`$this->selectSubquery('foo', 'bar');`

`$this->selectSubquery('baz', 'qux');`

**Anda juga dapat menggunakan metode ini secara berkelompok seperti berikut:**
```php
$this->selectSubquery([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Baca Juga
* [distinct](./distinct)
* [select](./select)
* [selectAvg](./selectAvg)
* [selectCount](./selectCount)
* [selectMax](./selectMax)
* [selectMin](./selectMin)
* [selectSum](./selectSum)
