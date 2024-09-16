Kontribusi kalian dibutuhkan!
Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

##### Referensi

`group_end($argumen_1, $argumen_2)`

##### Parameter
* **$argumen_1** (string) keterangan terkait variabel.
* **$argumen_2** (string) keterangan terkait variabel.

##### Contoh Penggunaan
`$this->group_end('foo', 'bar');`
`$this->group_end('baz', 'qux');`


##### Anda juga dapat menggunakan metode ini secara berkelompok, misalnya:
```php
$this->group_end([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

##### Baca juga
* [render](./render)
