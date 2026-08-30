`allowPublicFormSubmission()` mengizinkan form publik submit melalui pipeline CRUD Core.

### Tujuan
`allowPublicFormSubmission()` mengizinkan form publik submit melalui pipeline CRUD Core. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan di awal method controller ketika modul perlu perilaku permission, token, upload, database, debug, form publik, atau integrasi khusus.

### Referensi
`allowPublicFormSubmission(bool $return = true): static`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$return` | `bool` | Tidak | `true` | Flag boolean untuk mengaktifkan atau mematikan fitur. |

### Return
`static`

Mengembalikan instance controller saat ini, sehingga dapat dirangkai dengan method Core lain sebelum `render()`.

### Perilaku
`allowPublicFormSubmission()` mengizinkan submit form publik tetap melewati validasi dan persiapan data Core tanpa permission penuh.

> [!WARNING]
>
> Gunakan hanya untuk form publik yang memang harus submit lewat Core. Validasi, token, dan target penulisan tetap harus dibatasi.

### Contoh Dasar
```php
$this->allowPublicFormSubmission();

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->allowPublicFormSubmission();
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
            ->allowPublicFormSubmission();

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
* [allowTokenFrom](./allowTokenFrom)
* [validToken](./validToken)
* [permitUpsert](./permitUpsert)
* [setUploadPath](./setUploadPath)
* [render](./render)
