`setPermission()` menjalankan pemeriksaan akses modul dan pembatasan grup.

### Tujuan
`setPermission()` menjalankan pemeriksaan akses modul dan pembatasan grup. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan di awal method controller ketika modul perlu perilaku permission, token, upload, database, debug, form publik, atau integrasi khusus.

### Referensi
`setPermission(array|string $permissiveGroup = [], ?string $redirect = null): static|Response`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$permissiveGroup` | <code>array&#124;string</code> | Tidak | `[]` | ID grup yang diizinkan. Gunakan `0` untuk mengizinkan semua grup. |
| `$redirect` | `?string` | Tidak | `null` | Tujuan redirect saat akses ditolak atau aksi selesai. |

### Return
`static|Response`

Mengembalikan controller ketika akses diizinkan, atau response Aksara/CodeIgniter ketika akses ditolak atau dialihkan.

### Perilaku
`setPermission()` menandai request agar melewati pemeriksaan permission Core. Pemeriksaan memakai module, method, dan group yang aktif saat method ini dipanggil.

> [!IMPORTANT]
> Panggil `setPermission()` setelah `parentModule()` atau `setMethod()` bila keduanya dibutuhkan, karena permission dicek memakai modul dan method yang aktif saat itu.

### Contoh Dasar
```php
$this->setPermission([1, 2]);

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->setTitle(phrase('Pesanan'))
    ->setIcon('mdi mdi-cart-outline')
    ->setPermission([1, 2]);

return $this->render('orders');
```

### Contoh Lengkap
```php
namespace Modules\Pesanan\Controllers;

use Aksara\Laboratory\Core;

class Pesanan extends Core
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
