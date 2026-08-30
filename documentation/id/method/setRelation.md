`setRelation()` adalah Core method yang tersedia di dalam controller Aksara.

### Tujuan
`setRelation()` mengubah field menjadi pilihan relasi atau sumber autocomplete. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan ketika tabel, read view, atau form create/update bawaan sudah cukup, tetapi field tertentu perlu label, layout, validasi, relasi, visibility, atau renderer khusus.

### Referensi
`setRelation(string $field, string $primaryKey, string $output, array $where = [], array $join = [], array $orderBy = [], ?string $groupBy = null, int $limit = 0, bool $translate = false): static`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$field` | `string` | Ya | - | Field lokal yang menyimpan nilai relasi. |
| `$primaryKey` | `string` | Ya | - | Key tabel relasi, misalnya `categories.category_id`. |
| `$output` | `string` | Ya | - | Template tampilan relasi, misalnya `{{ categories.category_name }}`. |
| `$where` | `array` | Tidak | `[]` | Kondisi WHERE tambahan. |
| `$join` | `array` | Tidak | `[]` | Konfigurasi JOIN tambahan. |
| `$orderBy` | `array` | Tidak | `[]` | Aturan pengurutan. |
| `$groupBy` | `?string` | Tidak | `null` | Aturan pengelompokan. |
| `$limit` | `int` | Tidak | `0` | Jumlah maksimum row. |
| `$translate` | `bool` | Tidak | `false` | Menentukan apakah output dilewatkan ke fungsi translasi. |

### Nilai Kembali
`static`

Mengembalikan instance controller saat ini, sehingga dapat dirangkai dengan method Core lain sebelum `render()`.

### Perilaku
`setRelation()` memperbarui metadata field yang dibaca renderer tabel, read, form, API, dan dokumen. Efeknya muncul saat `render()` melakukan serialisasi dan menyiapkan response.

### Contoh Dasar
```php
$this->setRelation('category_id', 'app_categories.category_id', '{{ app_categories.category_name }}');

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->setRelation('category_id', 'app_categories.category_id', '{{ app_categories.category_name }}');
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
            ->setRelation('category_id', 'app_categories.category_id', '{{ app_categories.category_name }}');

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
* [setAutocomplete](./setAutocomplete)
* [setValidation](./setValidation)
* [setAlias](./setAlias)
* [addField](./addField)
