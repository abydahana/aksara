`afterUpdate()` menjalankan logika khusus setelah update berhasil.

### Tujuan
`afterUpdate()` menjalankan logika khusus setelah update berhasil. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Override ketika modul perlu pemeriksaan, persiapan data, logging, invalidasi cache, atau efek samping di sekitar operasi CRUD.

### Referensi
`afterUpdate()`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| Tidak ada | - | - | - | Metode ini tidak menerima parameter. |

### Return
`mixed|void`

Hook tidak wajib mengembalikan nilai. Kembalikan response atau exception Aksara hanya ketika operasi CRUD perlu dihentikan.

### Perilaku
`afterUpdate()` adalah hook protected opsional. Core memanggilnya otomatis pada titik CRUD yang sesuai ketika controller mendefinisikan atau meng-override metode ini.

### Contoh Dasar
```php
protected function afterUpdate()
{
    log_message('info', 'Pesanan berhasil diperbarui.');
}
```

### Contoh Lanjutan
```php
protected function afterUpdate()
{
    service('cache')->delete('ringkasan_pesanan');
}
```

### Contoh Lengkap
```php
namespace Modules\Pesanan\Controllers;

use Aksara\Laboratory\Core;

class Pesanan extends Core
{
    protected function afterUpdate()
    {
        service('cache')->delete('ringkasan_pesanan');
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
* [beforeDelete](./beforeDelete)
* [afterDelete](./afterDelete)
* [insertData](./insertData)
* [updateData](./updateData)
* [deleteData](./deleteData)
