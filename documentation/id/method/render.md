`render()` adalah Core method yang tersedia di dalam controller Aksara.

### Tujuan
`render()` menjalankan controller menjadi halaman, JSON API, dokumen, atau action CRUD. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan untuk modul lanjutan yang perlu menyentuh pipeline render, serialisasi, validasi, atau CRUD Core secara langsung. Modul biasa umumnya cukup memanggil `render()`.

### Referensi
`render(?string $table = null, ?string $view = null): mixed`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$table` | `?string` | Tidak | `null` | Nama tabel, dapat memakai alias jika metode mendukungnya. |
| `$view` | `?string` | Tidak | `null` | Path view khusus untuk mengganti view bawaan. |

### Nilai Kembali
`mixed`

Mengembalikan response akhir untuk request aktif: halaman, JSON, redirect, dokumen, atau response exception sesuai konteks.

### Perilaku
`render()` adalah dispatcher utama. Method ini memeriksa permission, membangun query, menangani action CRUD/export/print/pdf, memvalidasi form, memformat API, lalu mengembalikan response theme aktif.

### Contoh Dasar
```php
return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->setTitle(phrase('Pesanan'))
    ->setPrimary('order_id')
    ->setRelation('customer_id', 'customers.customer_id', '{{ customers.customer_name }}')
    ->where('orders.deleted_at', null);

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
            ->setPrimary('order_id')
            ->where('orders.deleted_at', null);

        return $this->render('orders');
    }
}
```

### Hasil
Metode mengembalikan payload terstruktur atau response object sesuai signature sambil menjaga validasi, hook, dan response Core.

### Catatan
* Sebagian besar modul cukup memakai `render()`; method pipeline level bawah hanya untuk kebutuhan lanjutan.
* Helper CRUD langsung tetap bergantung pada request state, metadata tabel, validasi, dan hook Core.

### Kesalahan Umum
* Melewati validasi atau hook Core tanpa sengaja lewat penulisan database mentah.
* Menganggap payload internal sebagai HTML final.
* Mengembalikan array mentah dari route yang mengharapkan response Aksara.

### Metode Terkait
* [renderTable](./renderTable)
* [renderRead](./renderRead)
* [renderForm](./renderForm)
* [serialize](./serialize)
* [validateForm](./validateForm)
