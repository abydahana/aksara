Kontribusi kalian dibutuhkan!
Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

##### Referensi

`not_like($argumen_1, $argumen_2)`

##### Parameter
* **$argumen_1** (string) keterangan terkait variabel.
* **$argumen_2** (string) keterangan terkait variabel.

##### Contoh Penggunaan
`$this->not_like('foo', 'bar');`
`$this->not_like('baz', 'qux');`


##### Anda juga dapat menggunakan metode ini secara berkelompok, misalnya:
```php
$this->not_like([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

##### Baca juga
* [like](./like)
* [or_like](./or_like)
* [or_not_like](./or_not_like)
