`databaseConfig()` memilih grup koneksi database yang digunakan oleh model Core.

### Tujuan
`databaseConfig()` memilih grup koneksi database yang digunakan oleh model Core. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan di awal method controller ketika modul perlu perilaku permission, token, upload, database, debug, form publik, atau integrasi khusus.

### Referensi
`databaseConfig(array|string $driver = [], ?string $hostname = null, ?int $port = null, ?string $username = null, ?string $password = null, ?string $database = null): static`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$driver` | `array\|string` | Tidak | `[]` | Nilai yang digunakan oleh metode ini. |
| `$hostname` | `?string` | Tidak | `null` | Nilai yang digunakan oleh metode ini. |
| `$port` | `?int` | Tidak | `null` | Nilai yang digunakan oleh metode ini. |
| `$username` | `?string` | Tidak | `null` | Nilai yang digunakan oleh metode ini. |
| `$password` | `?string` | Tidak | `null` | Nilai yang digunakan oleh metode ini. |
| `$database` | `?string` | Tidak | `null` | Nama grup koneksi database dari konfigurasi aplikasi. |

### Return
`static`

Mengembalikan instance controller saat ini, sehingga dapat dirangkai dengan method Core lain sebelum `render()`.

### Perilaku
`databaseConfig()` mengubah state runtime Core untuk request saat ini. Letakkan sebelum method yang bergantung padanya, terutama sebelum `setPermission()`, validasi, atau `render()`.

### Contoh Dasar
```php
$this->databaseConfig('default');

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->databaseConfig('default');
$this->setPermission([1, 2])
    ->setTitle(phrase('Pesanan'))
    ->setUploadPath('pesanan');

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
            ->databaseConfig('default');

        return $this->render('orders');
    }
}
```

### Hasil
Request saat ini memakai state Core tersebut saat permission, validasi, rendering, atau integrasi diproses.

### Catatan
* Metode ini chainable dan biasanya dipanggil sebelum `render()`.
* Urutan konfigurasi bisa berpengaruh; panggil sebelum method yang bergantung pada state tersebut.
* Batasi pengecualian public form dan token hanya pada route yang membutuhkannya.

### Kesalahan Umum
* Menaruh konfigurasi setelah `setPermission()`, validasi, atau `render()` ketika step tersebut sudah membutuhkannya.
* Membuat pengecualian public/token terlalu luas.

### Metode Terkait
* [setPermission](./setPermission)
* [allowPublicFormSubmission](./allowPublicFormSubmission)
* [allowTokenFrom](./allowTokenFrom)
* [validToken](./validToken)
* [permitUpsert](./permitUpsert)
* [setUploadPath](./setUploadPath)
* [render](./render)
