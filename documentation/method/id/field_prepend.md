Sesuai dengan nama metodenya, field prepend menambahkan komponen atau prefix pada field yang mana dapat berupa prefix keterangan dari sebuah elemen input.

### Referensi
`field_prepend($field, $prepend)`

**Parameter**
* **$field** [mixed] *nama daripada field;*
* **$prepend** [string] *isi prefix yang digunakan.*

&nbsp;

### Contoh Penggunaan
`$this->field_prepend('foo', 'bar');`

`$this->field_prepend('baz', 'qux');`

**Anda juga dapat menggunakan metode secara berkelompok seperti berikut:**
```php
$this->field_prepend([
    'foo' => 'bar',
    'baz' => 'qux'
]);
```

&nbsp;

### Baca Juga
* [field_append](./field_append)
