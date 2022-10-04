Metode ini jarang digunakan karena pada fungsinya, metode ini digunakan untuk menjalankan service latar belakang yang menjalankan `query builder`. Fungsi dari metode ini adalah untuk mendefinisikan dari mana table yang akan diambil untuk diproses datanya.

### Metode tidak perlu digunakan jika parameter dipanggil dalam metode `render()`

###### Referensi

`from($params)`

##### Parameter

* **$params** (string) - nama table database

###### Contoh penggunaan

`$this->from('products');`

###### Baca juga
* [render](./render)
