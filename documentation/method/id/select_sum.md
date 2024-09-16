Kontribusi kalian dibutuhkan!
Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

### Referensi
`select_sum($foo, $bar)`

**Parameter**
* **$foo** [`string`] *keterangan terkait variabel;*
* **$bar** [`string`] *keterangan terkait variabel.*

&nbsp;

### Contoh Penggunaan
`$this->select_sum('foo', 'bar');`

`$this->select_sum('baz', 'qux');`

**Anda juga dapat menggunakan metode ini secara berkelompok seperti berikut:**
```php
$this->select_sum([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Baca Juga
* [distinct](./distinct)
* [select](./select)
* [select_avg](./select_avg)
* [select_count](./select_count)
* [select_max](./select_max)
* [select_min](./select_min)
* [select_subquery](./select_subquery)
