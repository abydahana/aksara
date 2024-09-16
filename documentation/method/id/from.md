Metode ini jarang digunakan karena pada fungsinya, metode ini digunakan untuk menjalankan service latar belakang yang menjalankan `query builder`. Fungsi dari metode ini adalah untuk mendefinisikan dari mana table yang akan diambil untuk diproses datanya.

> [!NOTE]
> Metode tidak perlu digunakan jika parameter dipanggil dalam metode `render()`

&nbsp;

### Referensi
`from($table_name)`

**Parameter**
* **$table_name** [`string`] *nama table database.*

&nbsp;

### Contoh Penggunaan

`$this->from('foo');`

&nbsp;

### Baca Juga
* [table](./table)
* [from_subquery](./from_subquery)
