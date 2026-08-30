`setTheme()` adalah Core method yang tersedia di dalam controller Aksara.

### Tujuan
`setTheme()` mengganti theme yang digunakan untuk response controller. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan ketika interface bawaan perlu metadata halaman, action, tombol, filter, layout, atau output tambahan tanpa membuat view khusus.

### Referensi
`setTheme(string $theme = 'frontend'): static|bool`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$theme` | `string` | Tidak | `'frontend'` | Nilai yang digunakan oleh metode ini. |

### Nilai Kembali
`static|bool`

Mengembalikan controller saat theme diterima, atau `false` bila theme tidak dapat digunakan.

### Perilaku
`setTheme()` menyimpan konfigurasi interface pada controller. Renderer aktif membacanya untuk tombol, filter, heading, layout, variable theme, atau payload output.

### Contoh Dasar
```php
$this->setTheme('backend');

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->setTheme('backend');
$this->addToolbar('pesanan/laporan', phrase('Laporan'), 'btn btn-outline-primary', 'mdi mdi-chart-bar')
    ->setIcon('mdi mdi-cart')
    ->setTitle(phrase('Pesanan'));

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
            ->setTheme('backend');

        return $this->render('orders');
    }
}
```

### Hasil
Interface atau payload output bawaan mengikuti konfigurasi tanpa perlu view khusus.

### Catatan
* Gunakan `phrase()` untuk label yang terlihat agar UI tetap dapat diterjemahkan.
* Panggil konfigurasi UI sebelum `render()` agar renderer dapat membacanya.

### Kesalahan Umum
* Menulis label hard-code yang seharusnya memakai `phrase()`.
* Menambahkan action baris tanpa parameter primary key yang dibutuhkan URL tujuan.
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
