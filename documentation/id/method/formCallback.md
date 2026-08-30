`formCallback()` adalah Core method yang tersedia di dalam controller Aksara.

### Tujuan
`formCallback()` mendaftarkan callback form khusus untuk alur validasi. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan di awal method controller ketika modul perlu perilaku permission, token, upload, database, debug, form publik, atau integrasi khusus.

### Referensi
`formCallback(string $callback): static`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$callback` | `string` | Ya | - | Nama protected callback method di controller. |

### Nilai Kembali
`static`

Mengembalikan instance controller saat ini, sehingga dapat dirangkai dengan method Core lain sebelum `render()`.

### Perilaku
`formCallback()` mengubah state runtime Core untuk request saat ini. Letakkan sebelum method yang bergantung padanya, terutama sebelum `setPermission()`, validasi, atau `render()`.

### Contoh Dasar
```php
$this->formCallback('validasiPesanan');

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->formCallback('validasiPesanan');
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
            ->formCallback('validasiPesanan');

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
