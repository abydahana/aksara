Metode ini digunakan untuk melakukan pengelompokan klausa `where` apabila sebuah `controller` memerlukan `statement` yang berbeda dengan pengecualian tertentu untuk setiap klausa `where` yang dijalankan. Pada fungsi `query builder`, metode ini akan menghasilkan pengembalian seperti berikut:

```php
$this->group_start(); // mulai menjalankan pengelompokkan
$this->where('field_1', 'statement');
$this->or_where('field_2', 'statement');
$this->group_end(); // pengelompokan diakhiri

$this->where('field_3', 'statement'); // klausa di luar kelompok
```

Pada pemanggilan `query builder` di atas, akan mengembalikan perintah `SQL` sebagai berikut:

```sql
WHERE
	(
		field_1 = "statement" OR field_2 = "statement"
	)
	AND
	field_3 = "statement"
```

---

###### Referensi

`group_where($where)`

###### Parameter

* **$where** (mixed) - klausa untuk dijadikan statement

###### Contoh penggunaan

`$this->group_where('field_1', 'statement');`

Anda juga dapat menjalankan metode ini secara multiple seperti berikut:

```php
$this->group_where
(
	array
	(
		'field_1'		=> 'statement',
		'field_2'		=> 'statement'
	)
);
```
