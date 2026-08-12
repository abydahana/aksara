Ada kalanya untuk menjalankan suatu trigger javascript, memerlukan suatu *class identity* unik pada element tertentu. Pada kasusn pemanggilan metode `addClass` di sini, Anda akan menambahkan ekstra class pada bidang input.

### Reference

`addClass($field, $class_name)`

**Parameter**
* **$field** [`mixed`] *nama kolom inputan / field;*
* **$class_name** [`string`] *class yang akan ditambahkan.*

&nbsp;

### Usage Sample

`$this->addClass('foo', 'bar');`

`$this->addClass('baz', 'qux');`

Pemanggilan metode di atas akan menambah class CSS pada kolom input dan akan menghasilkan contoh output seperti berikut:

`<input name="foo" class="bar" />`

`<input name="baz" class="qux" />`

**Anda juga dapat menggunakan metode ini secara berkelompok, misalnya:**
```php
$this->addClass([
    'nama_lengkap' => 'extra-class',
    'alamat' => 'another-class'
]);
```

&nbsp;

### Read Also
* [setAttribute](./setAttribute)
* [setPlaceholder](./setPlaceholder)
* [setTooltip](./setTooltip)
