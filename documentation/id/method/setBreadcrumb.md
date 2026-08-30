`setBreadcrumb()` mengatur ulang breadcrumb yang ditampilkan halaman.

### Tujuan
`setBreadcrumb()` mengatur ulang breadcrumb yang ditampilkan halaman. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan ketika antarmuka bawaan perlu metadata halaman, aksi, tombol, filter, layout, atau output tambahan tanpa membuat view khusus.

### Referensi
`setBreadcrumb(array|string $params = [], ?string $value = null): static`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$params` | `array\|string` | Tidak | `[]` | Nilai, daftar nilai, atau pasangan key/value yang diterima metode ini. |
| `$value` | `?string` | Tidak | `null` | Nilai untuk field, option, kondisi, atau kontrol yang dibuat. |

### Return
`static`

Mengembalikan instance controller saat ini, sehingga dapat dirangkai dengan method Core lain sebelum `render()`.

### Perilaku
`setBreadcrumb()` menyimpan konfigurasi antarmuka pada controller. Renderer aktif membacanya untuk tombol, filter, heading, layout, variable theme, atau payload output.

### Contoh Dasar
```php
$this->setBreadcrumb([
    'pesanan' => phrase('Pesanan'),
    current_page() => phrase('Pesanan Lunas')
]);

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->setBreadcrumb([
    'pesanan' => phrase('Pesanan'),
    current_page() => phrase('Pesanan Lunas')
]);
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
        $this->setBreadcrumb([
            'pesanan' => phrase('Pesanan'),
            current_page() => phrase('Pesanan Lunas')
        ]);

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
* [addToolbar](./addToolbar)
* [addButton](./addButton)
* [addDropdown](./addDropdown)
* [addSubmitButton](./addSubmitButton)
* [setButton](./setButton)
* [unsetToolbar](./unsetToolbar)
