Kontribusi kalian dibutuhkan!
Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

##### Referensi

`or_where($argumen_1, $argumen_2)`

##### Parameter
* **$argumen_1** (string) keterangan terkait variabel.
* **$argumen_2** (string) keterangan terkait variabel.

##### Contoh Penggunaan
`$this->or_where('foo', 'bar');`
`$this->or_where('baz', 'qux');`


##### Anda juga dapat menggunakan metode ini secara berkelompok, misalnya:
```php
$this->or_where([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

##### Baca juga
* [field_prepend](./field_prepend)
