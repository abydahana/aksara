`beforeDelete()` menjalankan logika khusus sebelum delete.

### Tujuan
`beforeDelete()` menjalankan logika khusus sebelum delete. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Override ketika modul perlu pemeriksaan, persiapan data, logging, invalidasi cache, atau efek samping di sekitar operasi CRUD.

### Referensi
`beforeDelete()`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| Tidak ada | - | - | - | Metode ini tidak menerima parameter. |

### Return
`mixed|void`

Hook tidak wajib mengembalikan nilai. Kembalikan response atau exception Aksara hanya ketika operasi CRUD perlu dihentikan.

### Perilaku
`beforeDelete()` adalah hook protected opsional. Core memanggilnya otomatis pada titik CRUD yang sesuai ketika controller mendefinisikan atau meng-override metode ini.

### Contoh Dasar
```php
protected function beforeDelete()
{
    log_message('info', 'Memeriksa akses sebelum pesanan dihapus.');
}
```

### Contoh Lanjutan
```php
protected function beforeDelete()
{
    if (! get_userdata('is_admin')) {
        return throw_exception(403, phrase('Hanya administrator yang dapat menghapus data ini.'));
    }
}
```

### Contoh Lengkap
```php
namespace Modules\Pesanan\Controllers;

use Aksara\Laboratory\Core;

class Pesanan extends Core
{
    protected function beforeDelete()
    {
        if (! get_userdata('is_admin')) {
            return throw_exception(403, phrase('Hanya administrator yang dapat menghapus data ini.'));
        }
    }

    public function index()
    {
        return $this->render('orders');
    }
}
```

### Hasil
Hook berjalan otomatis pada operasi CRUD yang sesuai. Return response atau error dapat menghentikan operasi bila alurnya mendukung.

### Catatan
* Hook adalah protected method di controller dan dipanggil otomatis oleh Core.
* Jaga hook tetap fokus; pekerjaan eksternal yang lama lebih baik dipindah ke queue.

### Kesalahan Umum
* Membuat hook `public`, padahal seharusnya `protected`.
* Mengira hook berjalan di luar pipeline CRUD Core.
* Melakukan pekerjaan jaringan yang lambat secara sinkron di dalam request.

### Metode Terkait
* [beforeInsert](./beforeInsert)
* [afterInsert](./afterInsert)
* [beforeUpdate](./beforeUpdate)
* [afterUpdate](./afterUpdate)
* [afterDelete](./afterDelete)
* [insertData](./insertData)
* [updateData](./updateData)
* [deleteData](./deleteData)
