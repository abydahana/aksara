`setAiContext()` mengirim instruksi dan data referensi khusus modul ke asisten AI.

### Tujuan
`setAiContext()` mengirim instruksi dan data referensi khusus modul ke asisten AI. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan di awal method controller ketika modul perlu perilaku permission, token, upload, database, debug, form publik, atau integrasi khusus.

### Referensi
`setAiContext(array $context): static`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$context` | `array` | Ya | - | Map konteks AI berisi scope, instruksi, data, tone, audience, atau max_tokens. |

### Return
`static`

Mengembalikan instance controller saat ini, sehingga dapat dirangkai dengan method Core lain sebelum `render()`.

### Perilaku
`setAiContext()` menyimpan konteks AI per route agar handler AI dapat membaca scope, instruksi, dan data referensi modul.

> [!NOTE]
>
> Konteks AI di-cache per route untuk alur modul saat ini. Jaga data tetap ringkas dan jangan menyertakan secret.

### Contoh Dasar
```php
$this->setAiContext([
    'scope' => 'pesanan',
    'instructions' => 'Bantu pengguna menulis catatan pesanan yang singkat.',
    'tone' => 'profesional'
]);

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->setAiContext([
    'scope' => 'pesanan',
    'instructions' => 'Bantu pengguna menulis catatan pesanan yang singkat.',
    'tone' => 'profesional'
]);
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
        $this->setAiContext([
            'scope' => 'pesanan',
            'instructions' => 'Bantu pengguna menulis catatan pesanan yang singkat.',
            'tone' => 'profesional'
        ]);

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
