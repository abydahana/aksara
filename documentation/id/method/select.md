Kontribusi kalian dibutuhkan!

Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

### Referensi
`select($foo, $bar)`

**Parameter**
* **$foo** [`string`] *keterangan terkait variabel;*
* **$bar** [`string`] *keterangan terkait variabel.*

&nbsp;

### Contoh Penggunaan
`$this->select('foo', 'bar');`

`$this->select('baz', 'qux');`

**Anda juga dapat menggunakan metode ini secara berkelompok seperti berikut:**
```php
$this->select([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Baca Juga
* [distinct](./distinct)
* [selectAvg](./selectAvg)
* [selectCount](./selectCount)
* [selectMax](./selectMax)
* [selectMin](./selectMin)
* [selectSubquery](./selectSubquery)
* [selectSum](./selectSum)
