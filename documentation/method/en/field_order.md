Metode ini sama seperti [column_order](./column_order), perbedaannya adalah pada lokasi jenis sortir. Apabila `column_order` digunakan untuk sortir urutan kolom tabel, pada metode `field_order` digunakan untuk mengurutkan posisi daripada komponen input dalam formulir.

### Reference
`field_order($params)`

**Parameter**
* **$params** [`mixed`] *nama-nama field yang diprioritaskan untuk diurutkan pertama.*

&nbsp;

### Usage Sample
`$this->field_order('foo, bar, baz, qux');`

&nbsp;

### Read Also
* [column_order](./column_order)
* [view_order](./view_order)
