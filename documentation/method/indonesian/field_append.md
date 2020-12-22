Sesuai dengan nama metodenya, field append menambahkan komponen atau prefix pada field yang mana dapat berupa prefix keterangan dari sebuah elemen input.

###### Referensi

`field_append($params, $value)`

###### Parameter

* **$params** (mixed) - nama daripada field
* **$value** (string) - isi prefix yang digunakan

###### Contoh penggunaan

`$this->field_append('harga', 'Rp.');`

Anda juga dapat menggunakan metode secara multiple seperti berikut:

```php
$this->field_append
(
	array
	(
		'harga'		=> 'Rp.',
		'url'		=> 'http://'
	)
);
```
