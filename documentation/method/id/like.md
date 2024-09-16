Kontribusi kalian dibutuhkan!
Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

##### Referensi

`like($argumen_1, $argumen_2)`

##### Parameter
* **$argumen_1** (string) keterangan terkait variabel.
* **$argumen_2** (string) keterangan terkait variabel.

##### Contoh Penggunaan
`$this->like('foo', 'bar');`
`$this->like('baz', 'qux');`


##### Anda juga dapat menggunakan metode ini secara berkelompok, misalnya:
```php
$this->like([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

##### Baca juga
* [or_like](./or_like)
* [not_like](./not_like)
* [or_not_like](./or_not_like)
