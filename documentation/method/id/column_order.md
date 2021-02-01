Metode ini digunakan untuk men-sortir urutan yang ditampilkan pada kolom tabel.

###### Referensi
`column_order($column)`

###### Parameter
* **$column** (mixed) - daftar kolom yang diprioritaskan pada urutan pertama

###### Contoh Penggunaan
Tabel awal:

kolom_1 | kolom_2 | kolom_3
------------ | ------------- | -------------
Konten kolom_1 | Konten kolom_2 | Konten kolom_3
Konten lain kolom_1 | Konten lain kolom_2 | Konten lain kolom_3

Jalankan metode:

`$this->column_order('kolom_3, kolom_1, kolom_2');`

Hasil tabel:

kolom_3 | kolom_1 | kolom_2
------------ | ------------- | -------------
Konten kolom_3 | Konten kolom_1 | Konten kolom_2
Konten lain kolom_3 | Konten lain kolom_1 | Konten lain kolom_2

###### Baca juga
* [field_order](./field_order)
