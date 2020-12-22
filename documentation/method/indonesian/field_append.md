Sesuai dengan nama metodenya, field append menambahkan komponen atau suffix setelah field yang mana dapat berupa suffix keterangan dari sebuah elemen input.

###### Referensi

`field_append($params, $value)`

###### Parameter

* **$params** (mixed) - nama daripada field
* **$value** (string) - isi suffix yang digunakan

###### Contoh penggunaan

`$this->field_append('pajak', '%');`

Anda juga dapat menggunakan metode secara multiple seperti berikut:

```php
$this->field_append
(
	array
	(
		'pajak'		=> '%',
		'ukuran'	=> 'cm'
	)
);
```

###### Baca juga
* [field_prepend](./field_prepend)
