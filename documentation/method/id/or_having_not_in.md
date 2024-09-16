Kontribusi kalian dibutuhkan!

Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

### Referensi
`or_having_not_in($foo, $bar)`

**Parameter**
* **$foo** [`string`] *keterangan terkait variabel;*
* **$bar** [`string`] *keterangan terkait variabel.*

&nbsp;

### Contoh Penggunaan
`$this->or_having_not_in('foo', 'bar');`

`$this->or_having_not_in('baz', 'qux');`

**Anda juga dapat menggunakan metode ini secara berkelompok seperti berikut:**
```php
$this->or_having_not_in([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Baca Juga
* [having](./having)
* [or_having](./or_having)
* [or_having_in](./or_having_in)
* [having_in](./having_in)
* [having_not_in](./having_not_in)
* [having_like](./having_like)
* [or_having_like](./or_having_like)
* [not_having_like](./not_having_like)
* [or_not_having_like](./or_not_having_like)
