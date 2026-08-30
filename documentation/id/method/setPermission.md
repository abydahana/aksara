`setPermission()` adalah Core method yang tersedia di dalam controller Aksara.

### Tujuan
`setPermission()` menjalankan pemeriksaan akses modul dan pembatasan grup. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan di awal method controller ketika modul perlu perilaku permission, token, upload, database, debug, form publik, atau integrasi khusus.

### Referensi
`setPermission(array|string $permissiveGroup = [], ?string $redirect = null): static|Response`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$permissiveGroup` | `array|string` | Tidak | `[]` | ID grup yang diizinkan. Gunakan `0` untuk mengizinkan semua grup. |
| `$redirect` | `?string` | Tidak | `null` | Tujuan redirect saat akses ditolak atau action selesai. |

### Nilai Kembali
`static|Response`

Mengembalikan controller ketika akses diizinkan, atau response Aksara/CodeIgniter ketika akses ditolak atau dialihkan.

### Perilaku
`setPermission()` mengubah state runtime Core untuk request saat ini. Letakkan sebelum method yang bergantung padanya, terutama sebelum `setPermission()`, validasi, atau `render()`.

### Contoh Dasar
```php
$this->setPermission([1, 2]);

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->setPermission([1, 2]);
$this->setPermission([1, 2])
    ->setTitle(phrase('Pesanan'))
    ->setUploadPath('pesanan');

return $this->render('orders');
```

### Contoh Lengkap
```php
namespace Modules\Pesanan\Controllers;

use Aksara\Controllers\BaseController;

class Pesanan extends BaseController
{
    public function index()
    {
        $this->setTitle(phrase('Pesanan'))
            ->setPermission([1, 2]);

        return $this->render('orders');
    }
}
```

### Hasil
Request saat ini memakai state Core tersebut saat permission, validasi, rendering, atau integrasi diproses.

### Catatan
* Urutan konfigurasi bisa berpengaruh; panggil sebelum method yang bergantung pada state tersebut.
* Batasi pengecualian public form dan token hanya pada route yang membutuhkannya.

### Kesalahan Umum
* Menaruh konfigurasi setelah `setPermission()`, validasi, atau `render()` ketika step tersebut sudah membutuhkannya.
* Membuat pengecualian public/token terlalu luas.

### Metode Terkait
* [allowPublicFormSubmission](./allowPublicFormSubmission)
* [allowTokenFrom](./allowTokenFrom)
* [validToken](./validToken)
* [permitUpsert](./permitUpsert)
* [setUploadPath](./setUploadPath)
* [render](./render)
