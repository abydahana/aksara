Ada kalanya untuk menjalankan suatu trigger javascript, memerlukan suatu *class identity* unik pada element tertentu. Pada kasusn pemanggilan metode `add_class` di sini, Anda akan menambahkan ekstra class pada bidang input.

###### Referensi

`add_class($params, $value)`

###### Parameter

* **$params** (mixed) - nama kolom inputan / field,
* **$value** (string) - class yang akan ditambahkan.

###### Contoh Penggunaan

`$this->add_class('nama_lengkap', 'extra-class');`

Pemanggilan metode di atas akan menambah class CSS pada kolom input dan akan menghasilkan contoh output seperti berikut:

`<input name="nama_lengkap" class="extra-class" />`

Anda juga dapat menggunakan metode ini secara multiple, misalnya:

```php
$this->add_class
(
	array
	(
		'nama_lengkap'			=> 'extra-class',
		'alamat'				=> 'another-class'
	)
);
```
