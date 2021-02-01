Metode ini digunakan ketika akan melakukan validasi formulir menggunakan validasi pribadi. Artinya tidak menggunakan validasi yang secara default diberikan oleh framework. Pada kasus tertentu, apabila ingin mendapatkan suatu pengembalian yang rumit dari permintaan formulir, maka metode ini akan diperlukan.

###### Referensi

`form_callback($callback)`

###### Parameter

* **$callback** (string) - metode yang akan dipanggil dan dijalankan

###### Contoh penggunaan

`$this->form_callback('validasi');`

Pada contoh di atas, Anda harus membuat public method bernama `validasi` misal seperti berikut:

```php
public function validasi()
{
	// statement untuk menjalankan dan mengembalikan validasi
}
```
