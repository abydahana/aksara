Metode ini digunakan untuk mengatur ukuran masing-masing inputan apabila dilakukan pengelompokan menggunakan [merge_field](./merge_field). Secara default, ukuran bidang input yang terkelompok akan dibagi rata antar masing-masing ukurannya. Namun pada kasus tertentu, Anda mungkin membutuhkan pengelompokan dengan ukuran yang berbeda.

### Reference
`field_size($field, $size)`

**Parameter**
* **$field** [`mixed`] *nama daripada field;*
* **$size** [`string`] *class Bootstrap untuk menentukan ukuran.*

&nbsp;

### Usage Sample
`$this->field_size('foo', 'col-md-8');`

`$this->field_size('bar', 'col-md-8');`

**Anda juga dapat menjalankan metode ini secara berkelompok seperti berikut:**
```php
$this->field_size([
    'foo' => 'col-md-8',
    'bar' => 'col-md-4'
]);
```

Anda mungkin membaca ulasan terkait penggunaan **Bootstrap** Grid pada artikel berikut:
[https://getbootstrap.com/docs/5.3/layout/grid/](https://getbootstrap.com/docs/5.3/layout/grid/).

&nbsp;

### Read Also
* [column_size](./column_size)
* [modal_size](./modal_size)
