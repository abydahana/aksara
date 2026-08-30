`gridView()` menampilkan data tabel dalam layout grid menggunakan field thumbnail.

### Tujuan
`gridView()` menampilkan data tabel dalam layout grid menggunakan field thumbnail. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan ketika antarmuka bawaan perlu metadata halaman, aksi, tombol, filter, layout, atau output tambahan tanpa membuat view khusus.

### Referensi
`gridView(string $thumbnail, ?string $hyperlink = null, array $parameter = [], bool $newTab = false): static`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$thumbnail` | `string` | Ya | - | Field sumber thumbnail pada grid view. |
| `$hyperlink` | `?string` | Tidak | `null` | URL tujuan opsional untuk setiap item grid. |
| `$parameter` | `array` | Tidak | `[]` | Parameter tambahan untuk renderer, URL, atau query. |
| `$newTab` | `bool` | Tidak | `false` | Menentukan apakah aksi dibuka di tab baru. |

### Return
`static`

Mengembalikan instance controller saat ini, sehingga dapat dirangkai dengan method Core lain sebelum `render()`.

### Perilaku
`gridView()` menyimpan konfigurasi antarmuka pada controller. Renderer aktif membacanya untuk tombol, filter, heading, layout, variable theme, atau payload output.

### Contoh Dasar
```php
$this->gridView('thumbnail', 'pesanan/read', ['order_id' => 'order_id']);

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->gridView('thumbnail', 'pesanan/read', ['order_id' => 'order_id']);
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
            ->gridView('thumbnail', 'pesanan/read', ['order_id' => 'order_id']);

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
