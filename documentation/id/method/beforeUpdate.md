`beforeUpdate()` adalah Core method yang tersedia di dalam controller Aksara.

### Tujuan
`beforeUpdate()` menjalankan logika khusus sebelum update. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Override ketika modul perlu pemeriksaan, persiapan data, logging, invalidasi cache, atau efek samping di sekitar operasi CRUD.

### Referensi
`beforeUpdate()`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| Tidak ada | - | - | - | Metode ini tidak menerima parameter. |

### Nilai Kembali
`mixed|void`

Hook tidak wajib mengembalikan nilai. Kembalikan response atau exception Aksara hanya ketika operasi CRUD perlu dihentikan.

### Perilaku
`beforeUpdate()` adalah hook protected opsional. Core memanggilnya otomatis pada titik CRUD yang sesuai ketika controller mendefinisikan atau meng-override metode ini.

### Contoh Dasar
```php
protected function beforeUpdate()
{
    log_message('info', 'Memeriksa data sebelum pesanan diperbarui.');
}
```

### Contoh Lanjutan
```php
protected function beforeUpdate()
{
    if ($this->request->getPost('status') === 'lunas' && ! get_userdata('is_admin')) {
        return throw_exception(403, phrase('Hanya administrator yang dapat menandai pesanan lunas.'));
    }
}
```

### Contoh Lengkap
```php
namespace Modules\Pesanan\Controllers;

use Aksara\Controllers\BaseController;

class Pesanan extends BaseController
{
    protected function beforeUpdate()
    {
        if ($this->request->getPost('status') === 'lunas' && ! get_userdata('is_admin')) {
            return throw_exception(403, phrase('Hanya administrator yang dapat menandai pesanan lunas.'));
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
* [afterUpdate](./afterUpdate)
* [beforeDelete](./beforeDelete)
* [afterDelete](./afterDelete)
* [insertData](./insertData)
* [updateData](./updateData)
* [deleteData](./deleteData)
