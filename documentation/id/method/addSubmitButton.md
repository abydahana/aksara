`addSubmitButton()` menambahkan tombol di dekat tombol submit form.

### Tujuan
`addSubmitButton()` menambahkan tombol di dekat tombol submit form. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan ketika antarmuka bawaan perlu metadata halaman, aksi, tombol, filter, layout, atau output tambahan tanpa membuat view khusus.

### Referensi
`addSubmitButton(?string $name, ?string $value, string $label, ?string $class = null, ?string $icon = null, ?string $attribution = null): static`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$name` | `?string` | Ya | - | Nama tombol submit yang dikirim. |
| `$value` | `?string` | Ya | - | Nilai untuk field, option, kondisi, atau kontrol yang dibuat. |
| `$label` | `string` | Ya | - | Label yang dibaca pengguna. Gunakan `phrase()` untuk teks UI. |
| `$class` | `?string` | Tidak | `null` | Daftar class CSS untuk kontrol yang dibuat. |
| `$icon` | `?string` | Tidak | `null` | Class ikon untuk kontrol yang dibuat. |
| `$attribution` | `?string` | Tidak | `null` | Attribute mentah tambahan untuk elemen yang dibuat. |

### Return
`static`

Mengembalikan instance controller saat ini, sehingga dapat dirangkai dengan method Core lain sebelum `render()`.

### Perilaku
`addSubmitButton()` menyimpan konfigurasi antarmuka pada controller. Renderer aktif membacanya untuk tombol, filter, heading, layout, variable theme, atau payload output.

### Contoh Dasar
```php
$this->addSubmitButton('simpan_lanjut', '1', phrase('Simpan dan Lanjut'), 'btn btn-outline-primary', 'mdi mdi-content-save');

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->addSubmitButton('simpan_lanjut', '1', phrase('Simpan dan Lanjut'), 'btn btn-outline-primary', 'mdi mdi-content-save');
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
            ->addSubmitButton('simpan_lanjut', '1', phrase('Simpan dan Lanjut'), 'btn btn-outline-primary', 'mdi mdi-content-save');

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
* [setButton](./setButton)
* [unsetToolbar](./unsetToolbar)
* [render](./render)
