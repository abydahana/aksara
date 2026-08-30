`beforeInsert()` adalah Core method yang tersedia di dalam controller Aksara.

### Tujuan
`beforeInsert()` menjalankan logika khusus sebelum create. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Override ketika modul perlu pemeriksaan, persiapan data, logging, invalidasi cache, atau efek samping di sekitar operasi CRUD.

### Referensi
`beforeInsert()`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| Tidak ada | - | - | - | Metode ini tidak menerima parameter. |

### Nilai Kembali
`mixed|void`

Hook tidak wajib mengembalikan nilai. Kembalikan response atau exception Aksara hanya ketika operasi CRUD perlu dihentikan.

### Perilaku
`beforeInsert()` adalah hook protected opsional. Core memanggilnya otomatis pada titik CRUD yang sesuai ketika controller mendefinisikan atau meng-override metode ini.

### Contoh Dasar
```php
protected function beforeInsert()
{
    log_message('info', 'Menyiapkan data sebelum pesanan dibuat.');
}
```

### Contoh Lanjutan
```php
protected function beforeInsert()
{
    if (! get_userdata('is_logged')) {
        return throw_exception(403, phrase('Sesi Anda telah berakhir.'));
    }
}
```

### Contoh Lengkap
```php
namespace Modules\Pesanan\Controllers;

use Aksara\Controllers\BaseController;

class Pesanan extends BaseController
{
    protected function beforeInsert()
    {
        if (! get_userdata('is_logged')) {
            return throw_exception(403, phrase('Sesi Anda telah berakhir.'));
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
* [afterInsert](./afterInsert)
* [beforeUpdate](./beforeUpdate)
* [afterUpdate](./afterUpdate)
* [beforeDelete](./beforeDelete)
* [afterDelete](./afterDelete)
* [insertData](./insertData)
* [updateData](./updateData)
* [deleteData](./deleteData)
