Metode ini digunakan untuk mengatur ukuran masing-masing inputan apabila dilakukan pengelompokan menggunakan [merge_field](./merge_field). Secara default, ukuran bidang input yang terkelompok akan dibagi rata antar masing-masing ukurannya. Namun pada kasus tertentu, Anda mungkin membutuhkan pengelompokan dengan ukuran yang berbeda.

###### Referensi

`field_size($params, $value)`

###### Parameter

* **$params** (mixed) - nama daripada field
* **$value** (string) - class Bootstrap untuk menentukan ukuran

###### Contoh penggunaan

`$this->field_size('hostname', 'col-md-8');`

Anda juga dapat menjalankan metode ini secara multiple seperti berikut:

```php
$this->field_size
(
	array
	(
		'hostname'		=> 'col-md-8',
		'port'			=> 'col-md-4'
	)
);
```

Anda mungkin membaca ulasan terkait penggunaan Bootstrap Grid pada artikel berikut:

[https://getbootstrap.com/docs/4.5/layout/grid/](https://getbootstrap.com/docs/4.5/layout/grid/).

###### Baca juga
* [column_size](./column_size)
