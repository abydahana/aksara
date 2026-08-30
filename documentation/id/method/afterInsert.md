`afterInsert()` adalah Core method yang tersedia di dalam controller Aksara.

### Tujuan
`afterInsert()` menjalankan logika khusus setelah create berhasil. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Override ketika modul perlu pemeriksaan, persiapan data, logging, invalidasi cache, atau efek samping di sekitar operasi CRUD.

### Referensi
`afterInsert()`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| Tidak ada | - | - | - | Metode ini tidak menerima parameter. |

### Nilai Kembali
`mixed|void`

Hook tidak wajib mengembalikan nilai. Kembalikan response atau exception Aksara hanya ketika operasi CRUD perlu dihentikan.

### Perilaku
`afterInsert()` adalah hook protected opsional. Core memanggilnya otomatis pada titik CRUD yang sesuai ketika controller mendefinisikan atau meng-override metode ini.

### Contoh Dasar
```php
protected function afterInsert()
{
    log_message('info', 'Pesanan baru berhasil dibuat.');
}
```

### Contoh Lanjutan
```php
protected function afterInsert()
{
    service('cache')->delete('ringkasan_pesanan');
}
```

### Contoh Lengkap
```php
namespace Modules\Pesanan\Controllers;

use Aksara\Controllers\BaseController;

class Pesanan extends BaseController
{
    protected function afterInsert()
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
* [beforeUpdate](./beforeUpdate)
* [afterUpdate](./afterUpdate)
* [beforeDelete](./beforeDelete)
* [afterDelete](./afterDelete)
* [insertData](./insertData)
* [updateData](./updateData)
* [deleteData](./deleteData)
