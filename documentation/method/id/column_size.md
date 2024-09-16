Metode ini digunakan ketika akan mengatur sebuah ukuran kolom pada formulir CRUD. Misal untuk jenis formulir yang menggunakan lebih dari satu kolom seperti berikut:

![image](https://user-images.githubusercontent.com/10624446/102869707-9f061780-446e-11eb-8baa-25f91a767f90.png)

Pada contoh gambar di atas, formulir `modal` menggunakan 2 kolom, kolom 1 lebih lebar dari kolom 2. Ukuran kolom menggunakan class dari boorstrap (CSS framework).

### Referensi
`column_size($column, $size)`

**Parameter**
* **$column** [`mixed`] *inisial (nomor) kolom, dimulai dari: 1;*
* **$size** [`string`] *class (CSS) untuk mengatur ukuran.*

&nbsp;

### Contoh Penggunaan
`$this->column_size(1, 'col-md-8');`

**Anda juga dapat menjalankan metode ini secara berkelompok seperti berikut:**
```php
$this->column_size([
    1 => 'col-md-8',
    2 => 'col-md-4'
]);
```

**Pengertian dari parameter di atas adalah:**
Kolom 1 menggunakan class `col-md-8` dan kolom 2 menggunakan class `col-md-4`.

Referensi lain terkait inisial class yang tersedia, silakan merujuk pada penggunaan grid pada **Bootstrap** pada tautan berikut:

[https://getbootstrap.com/docs/5.3/layout/grid/](https://getbootstrap.com/docs/5.3/layout/grid/).

&nbsp;

### Baca Juga
* [field_size](./field_size)
* [modal_size](./modal_size)
