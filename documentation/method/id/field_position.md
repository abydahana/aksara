Metode ini berfungsi untuk mengontrol tata letak penempatan sebuah input pada formulir (field input). Apabila Anda mengatur kolom formulir menjadi beberapa kolom, metode ini dapat digunakan untuk mengatur posisi tata letak inputnya.

###### Referensi

`field_position($params, $value)`

###### Parameter

* **$params** (mixed) - nama daripada field
* **$value** (integer) - posisi target kolom

###### Contoh penggunaan

`$this->field_position('alamat', 2);`

Pada contoh penggunaan di atas, input untuk field `alamat` akan diposisikan pada kolom ke-dua.

Anda juga dapat menjalankan metode secara multiple seperti berikut:

```php
$this->field_position
(
	array
	(
		'alamat'		=> 2,
		'telepon'		=> 2,
		'status'		=> 3
	)
);
```
