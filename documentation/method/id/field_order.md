Metode ini sama seperti [column_order](./column_order), perbedaannya adalah pada lokasi jenis sortir. Apabila `column_order` digunakan untuk sortir urutan kolom tabel, pada metode `field_order` digunakan untuk mengurutkan posisi daripada komponen input dalam formulir.

###### Referensi

`field_order($params)`

###### Parameter

* **$params** (mixed) - nama-nama field yang diprioritaskan untuk diurutkan pertama

###### Contoh penggunaan

`$this->field_order('nama_depan, nama_belakang, jenis_kelamin, alamat');`

###### Baca juga
* [column_order](./column_order)
