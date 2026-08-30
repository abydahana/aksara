`validToken()` memvalidasi token CSRF yang dikirim atau konteks token API yang dipercaya.

### Tujuan
`validToken()` memvalidasi token CSRF yang dikirim atau konteks token API yang dipercaya. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan di awal method controller ketika modul perlu perilaku permission, token, upload, database, debug, form publik, atau integrasi khusus.

### Referensi
`validToken(?string $token, string|array $allowedUris = []): bool`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$token` | `?string` | Ya | - | Token CSRF atau token API yang dikirim. |
| `$allowedUris` | <code>string&#124;array</code> | Tidak | `[]` | URI atau daftar URI yang tokennya boleh diterima. |

### Return
`bool`

Mengembalikan `true` ketika pemeriksaan berhasil dan `false` ketika gagal.

### Perilaku
`validToken()` membandingkan token yang dikirim dengan token route saat ini atau URI lain yang diizinkan.

> [!CAUTION]
>
> Jangan melewati validasi token untuk submit form dari browser. API client punya jalur token sendiri, tetapi request POST normal sebaiknya tetap memakai proteksi CSRF.

### Contoh Dasar
```php
$valid = $this->validToken($this->request->getPost('_token'));
```

### Contoh Lanjutan
```php
$token = $this->request->getPost('_token');

if (! $this->validToken($token, ['pesanan/create'])) {
    return throw_exception(403, phrase('Token keamanan tidak valid.'));
}
```

### Contoh Lengkap
```php
namespace Modules\Pesanan\Controllers;

use Aksara\Laboratory\Core;

class Pesanan extends Core
{
    public function index()
    {
        $valid = $this->validToken($this->request->getPost('_token'));

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
* [allowTokenFrom](./allowTokenFrom)
* [allowPublicFormSubmission](./allowPublicFormSubmission)
* [setPermission](./setPermission)
