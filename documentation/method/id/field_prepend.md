Sesuai dengan nama metodenya, field prepend menambahkan komponen atau prefix pada field yang mana dapat berupa prefix keterangan dari sebuah elemen input.

###### Referensi

`field_prepend($params, $value)`

###### Parameter

* **$params** (mixed) - nama daripada field
* **$value** (string) - isi prefix yang digunakan

###### Contoh penggunaan

`$this->field_prepend('harga', 'Rp.');`

Anda juga dapat menggunakan metode secara multiple seperti berikut:

```php
$this->field_prepend
(
	array
	(
		'harga'		=> 'Rp.',
		'url'		=> 'http://'
	)
);
```


###### Baca juga

* [field_append](./field_append)
