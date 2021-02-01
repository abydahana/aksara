Metode ini digunakan apabila ingin memberikan nilai default pada suatu kolom input, namun tetap dapat diubah oleh pengguna aplikasi.

###### Referensi

`default_value($field, $value)`

###### Parameter
* **$field** (mixed) - nama bidang (field) yang akan diberikan nilai
* **$value** (mixed) - nilai yang akan digunakan

###### Contoh Penggunaan

`$this->default_value('nama_lengkap', 'John Doe');`

Parameter di atas akan menghasilkan sebuah bidang input seperti berikut:

`<input type="text" name="nama_lengkap" value="John Doe" />`

Anda juga dapat menjalankan metode secara multiple seperti berikut:

```php
$this->default_value
(
	array
	(
		'nama_lengkap'			=> 'John Doe',
		'usia'					=> 20
	)
);
```
