Metode ini digunakan untuk mengelompokkan suatu input pada formulir menjadi satu bagian. Misalnya pada penyusunan formulir yang mengharuskan untuk mengelompokkan beberapa input dalam satu element dasar untuk contoh kasus penggunaan `fieldset` pada formulir.

> [!NOTE]
> Metode ini masih dalam riset dan belum dapat digunakan!

### Referensi
`groupField($params, $group)`

**Parameter**
* **$params** [mixed] *nama field yang akan dilakukan pengelompokan;*
* **$group** [string] *inisial pengelompokan.*

&nbsp;

### Contoh penggunaan
`$this->groupField('foo', 'bar');`

`$this->groupField('baz', 'qux');`

**Anda juga dapat menjalankan metode ini secara berkelompok seperti contoh berikut:**
```php
$this->groupField([
    'nama_depan' => 'nama',
    'nama_belakang' => 'nama'
]);
```
