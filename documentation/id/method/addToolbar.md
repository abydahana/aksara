`addToolbar()` menambahkan aksi toolbar di atas tabel.

### Tujuan
`addToolbar()` menambahkan aksi toolbar di atas tabel. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan ketika antarmuka bawaan perlu metadata halaman, aksi, tombol, filter, layout, atau output tambahan tanpa membuat view khusus.

### Referensi
`addToolbar(string $url, string $label, ?string $class = null, ?string $icon = null, ?array $parameter = [], bool $newTab = false, ?string $attribution = null): static`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$url` | `string` | Ya | - | URL tujuan atau array definisi aksi. |
| `$label` | `string` | Ya | - | Label yang dibaca pengguna. Gunakan `phrase()` untuk teks UI. |
| `$class` | `?string` | Tidak | `null` | Daftar class CSS untuk kontrol yang dibuat. |
| `$icon` | `?string` | Tidak | `null` | Class ikon untuk kontrol yang dibuat. |
| `$parameter` | `?array` | Tidak | `[]` | Parameter tambahan untuk renderer, URL, atau query. |
| `$newTab` | `bool` | Tidak | `false` | Menentukan apakah aksi dibuka di tab baru. |
| `$attribution` | `?string` | Tidak | `null` | Attribute mentah tambahan untuk elemen yang dibuat. |

### Return
`static`

Mengembalikan instance controller saat ini, sehingga dapat dirangkai dengan method Core lain sebelum `render()`.

### Perilaku
`addToolbar()` menyimpan konfigurasi antarmuka pada controller. Renderer aktif membacanya untuk tombol, filter, heading, layout, variable theme, atau payload output.

### Contoh Dasar
```php
$this->addToolbar('pesanan/laporan', phrase('Laporan'), 'btn btn-outline-primary', 'mdi mdi-chart-bar');

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->addToolbar('pesanan/laporan', phrase('Laporan'), 'btn btn-outline-primary', 'mdi mdi-chart-bar');
$this->addToolbar('pesanan/laporan', phrase('Laporan'), 'btn btn-outline-primary', 'mdi mdi-chart-bar')
    ->setIcon('mdi mdi-cart')
    ->setTitle(phrase('Pesanan'));

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
            ->addToolbar('pesanan/laporan', phrase('Laporan'), 'btn btn-outline-primary', 'mdi mdi-chart-bar');

        return $this->render('orders');
    }
}
```

### Hasil
Antarmuka atau payload output bawaan mengikuti konfigurasi tanpa perlu view khusus.

### Catatan
* Metode ini chainable dan biasanya dipanggil sebelum `render()`.
* Gunakan `phrase()` untuk label yang terlihat agar UI tetap dapat diterjemahkan.
* Panggil konfigurasi UI sebelum `render()` agar renderer dapat membacanya.

### Kesalahan Umum
* Menulis label hard-code yang seharusnya memakai `phrase()`.
* Menambahkan aksi baris tanpa parameter primary key yang dibutuhkan URL tujuan.
* Memanggil metode terlalu lambat setelah output dibuat.

### Metode Terkait
* [setTitle](./setTitle)
* [setIcon](./setIcon)
* [addButton](./addButton)
* [addDropdown](./addDropdown)
* [addSubmitButton](./addSubmitButton)
* [setButton](./setButton)
* [unsetToolbar](./unsetToolbar)
* [render](./render)
