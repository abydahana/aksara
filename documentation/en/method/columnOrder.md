Metode ini digunakan untuk men-sortir urutan yang ditampilkan pada kolom tabel.

### Reference
`columnOrder($columns)`

**Parameter**
* **$columns** [`mixed`] *daftar kolom yang diprioritaskan pada urutan pertama.*

&nbsp;

### Usage Sample
**Tabel awal:**
kolom_1 | kolom_2 | kolom_3
------------ | ------------- | -------------
Konten kolom_1 | Konten kolom_2 | Konten kolom_3
Konten lain kolom_1 | Konten lain kolom_2 | Konten lain kolom_3

**Tambahkan metode:**
`$this->columnOrder('kolom_3, kolom_1, kolom_2');`

**Hasil tabel:**
kolom_3 | kolom_1 | kolom_2
------------ | ------------- | -------------
Konten kolom_3 | Konten kolom_1 | Konten kolom_2
Konten lain kolom_3 | Konten lain kolom_1 | Konten lain kolom_2

&nbsp;

### Read Also
* [fieldOrder](./fieldOrder)
* [viewOrder](./viewOrder)
