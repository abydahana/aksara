Kontribusi kalian dibutuhkan!
Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

##### Referensi

`from_subquery($argumen_1, $argumen_2)`

##### Parameter
* **$argumen_1** (string) keterangan terkait variabel.
* **$argumen_2** (string) keterangan terkait variabel.

##### Contoh Penggunaan
`$this->from_subquery('foo', 'bar');`
`$this->from_subquery('baz', 'qux');`


##### Anda juga dapat menggunakan metode ini secara berkelompok, misalnya:
```php
$this->from_subquery([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

##### Baca juga
* [render](./render)
