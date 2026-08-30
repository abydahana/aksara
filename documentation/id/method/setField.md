`setField()` adalah Core method yang tersedia di dalam controller Aksara.

### Tujuan
`setField()` mengatur tipe renderer dan parameter khusus field. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan ketika tabel, read view, atau form create/update bawaan sudah cukup, tetapi field tertentu perlu label, layout, validasi, relasi, visibility, atau renderer khusus.

### Referensi
`setField(string|array $field = [], string|array|null $type = null, array|string|null $parameter = null, mixed $alpha = null, mixed $beta = null, mixed $charlie = null, ?string $delta = null): static`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$field` | `string|array` | Tidak | `[]` | Nama field, daftar field dipisah koma, atau map field asosiatif. |
| `$type` | `string|array|null` | Tidak | `null` | Tipe renderer seperti `text`, `textarea`, `wysiwyg`, `image`, `select`, `boolean`, atau `custom`. |
| `$parameter` | `array|string|null` | Tidak | `null` | Parameter tambahan untuk renderer, URL, atau query. |
| `$alpha` | `mixed` | Tidak | `null` | Parameter renderer tambahan. |
| `$beta` | `mixed` | Tidak | `null` | Parameter renderer tambahan. |
| `$charlie` | `mixed` | Tidak | `null` | Parameter renderer tambahan. |
| `$delta` | `?string` | Tidak | `null` | Parameter renderer tambahan. |

### Nilai Kembali
`static`

Mengembalikan instance controller saat ini, sehingga dapat dirangkai dengan method Core lain sebelum `render()`.

### Perilaku
`setField()` memperbarui metadata field yang dibaca renderer tabel, read, form, API, dan dokumen. Efeknya muncul saat `render()` melakukan serialisasi dan menyiapkan response.

### Contoh Dasar
```php
$this->setField('description', 'wysiwyg');

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->setField('description', 'wysiwyg');
$this->setAlias('order_number', phrase('Nomor Pesanan'))
    ->setValidation('status', 'required|in_list[draft,lunas,batal]')
    ->fieldOrder('order_number, customer_id, status, notes');

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
            ->setField('description', 'wysiwyg');

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
* [addField](./addField)
* [setRelation](./setRelation)
* [setValidation](./setValidation)
* [setAlias](./setAlias)
* [setPlaceholder](./setPlaceholder)
* [setPrimary](./setPrimary)
* [unsetField](./unsetField)
* [render](./render)
