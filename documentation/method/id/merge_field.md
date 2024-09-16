Kontribusi kalian dibutuhkan!
Silakan perbarui halaman ini melalui GitHub dengan menggunakan format standar berikut dilengkapi dengan kalimat pembukaan.

##### Referensi

`merge_field($argumen_1, $argumen_2)`

##### Parameter
* **$argumen_1** (string) keterangan terkait variabel.
* **$argumen_2** (string) keterangan terkait variabel.

##### Contoh Penggunaan
`$this->merge_field('foo', 'bar');`
`$this->merge_field('baz', 'qux');`


##### Anda juga dapat menggunakan metode ini secara berkelompok, misalnya:
```php
$this->merge_field([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

##### Baca juga
* [merge_content](./merge_content)
