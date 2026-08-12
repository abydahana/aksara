Metode ini digunakan ketika akan melakukan validasi formulir menggunakan validasi pribadi. Artinya tidak menggunakan validasi yang secara default diberikan oleh framework. Pada kasus tertentu, apabila ingin mendapatkan suatu pengembalian yang rumit dari permintaan formulir, maka metode ini akan diperlukan.

### Reference
`formCallback($callback)`

**Parameter**
* **$callback** [`string`] *metode yang akan dipanggil dan dijalankan.*

&nbsp;

### Usage Sample
`$this->formCallback('foo');`

Pada contoh di atas, Anda harus membuat public method bernama `validasi` misal seperti berikut:
```php
public function foo()
{
    // Statement untuk menjalankan dan mengembalikan validasi
}
```

&nbsp;

### Read Also
* [validateForm](./validateForm)
* [validToken](./validToken)
