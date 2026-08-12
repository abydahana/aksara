Metode ini berfungsi untuk mengontrol tata letak penempatan sebuah input pada formulir (field input). Apabila Anda mengatur kolom formulir menjadi beberapa kolom, metode ini dapat digunakan untuk mengatur posisi tata letak inputnya.

### Reference
`fieldPosition($field, $position)`

**Parameter**
* **$field** [`mixed`] *nama daripada field;*
* **$position** [`integer`] *posisi target kolom.*

&nbsp;

### Usage Sample
`$this->fieldPosition('foo', 2);`

`$this->fieldPosition('bar', 2);`

Pada contoh penggunaan di atas, input untuk field `foo` dan `bar` akan diposisikan pada kolom ke-dua.

**Anda juga dapat menjalankan metode secara berkelompok seperti berikut:**
```php
$this->fieldPosition([
    'foo' => 2,
    'bar' => 2,
    'baz' => 3,
    'qux' => 4
]);
```

&nbsp;

### Read Also
* [fieldSize](./fieldSize)
* [columnSize](./columnSize)
