Metode ini jarang digunakan karena pada fungsinya, metode ini digunakan untuk menjalankan service latar belakang yang menjalankan `query builder`. Fungsi dari metode ini adalah untuk mendefinisikan dari mana table yang akan diambil untuk diproses datanya.

> [!NOTE]
> Metode tidak perlu digunakan jika parameter dipanggil dalam metode `render()`

&nbsp;

### Reference
`from($table_name)`

**Parameter**
* **$table_name** [`string`] *nama table database.*

&nbsp;

### Usage Sample

`$this->from('foo');`

&nbsp;

### Read Also
* [table](./table)
* [from_subquery](./from_subquery)
