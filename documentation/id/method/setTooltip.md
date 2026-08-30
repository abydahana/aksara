`setTooltip()` menambahkan tooltip atau bantuan singkat pada field.

### Tujuan
`setTooltip()` menambahkan tooltip atau bantuan singkat pada field. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan ketika tabel, read view, atau form create/update bawaan sudah cukup, tetapi field tertentu perlu label, layout, validasi, relasi, visibility, atau renderer khusus.

### Referensi
`setTooltip(string|array $params = [], ?string $value = null): static`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$params` | `string\|array` | Tidak | `[]` | Nilai, daftar nilai, atau pasangan key/value yang diterima metode ini. |
| `$value` | `?string` | Tidak | `null` | Nilai untuk field, option, kondisi, atau kontrol yang dibuat. |

### Return
`static`

Mengembalikan instance controller saat ini, sehingga dapat dirangkai dengan method Core lain sebelum `render()`.

### Perilaku
`setTooltip()` memperbarui metadata field yang dibaca renderer tabel, read, form, API, dan dokumen. Efeknya muncul saat `render()` melakukan serialisasi dan menyiapkan response.

### Contoh Dasar
```php
$this->setTooltip('status', phrase('Pilih status pembayaran saat ini.'));

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->setTooltip('status', phrase('Pilih status pembayaran saat ini.'));
$this->setAlias('order_number', phrase('Nomor Pesanan'))
    ->setValidation('status', 'required|in_list[draft,lunas,batal]')
    ->fieldOrder('order_number, customer_id, status, notes');

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
            ->setTooltip('status', phrase('Pilih status pembayaran saat ini.'));

        return $this->render('orders');
    }
}
```

### Hasil
Tabel, read view, form, dan payload API terformat memakai metadata field yang sudah diperbarui.

### Catatan
* Metode ini chainable dan biasanya dipanggil sebelum `render()`.
* Nama field harus cocok dengan kolom terpilih, alias, alias relasi, atau field virtual dari `addField()`.
* Gunakan `phrase()` untuk label, heading, placeholder, dan teks lain yang terlihat pengguna.

### Kesalahan Umum
* Memakai nama field yang tidak ada di data terpilih.
* Menulis label atau option tanpa `phrase()` padahal teks terlihat pengguna.
* Mengira metode ini langsung mencetak HTML, padahal hanya mengatur renderer.

### Metode Terkait
* [setField](./setField)
* [addField](./addField)
* [setRelation](./setRelation)
* [setValidation](./setValidation)
* [setAlias](./setAlias)
* [setPlaceholder](./setPlaceholder)
* [setPrimary](./setPrimary)
* [unsetField](./unsetField)
