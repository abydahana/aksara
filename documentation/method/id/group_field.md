Metode ini digunakan untuk mengelompokkan suatu input pada formulir menjadi satu bagian. Misalnya pada penyusunan formulir yang mengharuskan untuk mengelompokkan beberapa input dalam satu element dasar untuk contoh kasus penggunaan `fieldset` pada formulir.

#### Metode ini masih dalam riset dan belum dapat digunakan!

---

###### Referensi

`group_field($params, $group)`

###### Parameter

* **$params** (mixed) - nama field yang akan dilakukan pengelompokan
* **$group** (string) - inisial pengelompokan

###### Contoh penggunaan

`$this->group_field('nama_depan', 'nama');`

Anda juga dapat menjalankan metode ini secara multiple seperti contoh berikut:

```php
$this->group_field
(
	array
	(
		'nama_depan'		=> 'nama',
		'nama_belakang'		=> 'nama'
	)
);
```
