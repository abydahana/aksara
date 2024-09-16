Metode ini digunakan ketika akan melakukan validasi formulir menggunakan validasi pribadi. Artinya tidak menggunakan validasi yang secara default diberikan oleh framework. Pada kasus tertentu, apabila ingin mendapatkan suatu pengembalian yang rumit dari permintaan formulir, maka metode ini akan diperlukan.

### Reference
`form_callback($callback)`

**Parameter**
* **$callback** [`string`] *metode yang akan dipanggil dan dijalankan.*

&nbsp;

### Usage Sample
`$this->form_callback('foo');`

Pada contoh di atas, Anda harus membuat public method bernama `validasi` misal seperti berikut:
```php
public function foo()
{
    // Statement untuk menjalankan dan mengembalikan validasi
}
```

&nbsp;

### Read Also
* [validate_form](./validate_form)
* [valid_token](./valid_token)
