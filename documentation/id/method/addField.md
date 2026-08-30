`addField()` menambahkan field virtual yang tidak ada secara fisik di schema tabel.

### Tujuan
`addField()` menambahkan field virtual yang tidak ada secara fisik di schema tabel. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan ketika tabel, read view, atau form create/update bawaan sudah cukup, tetapi field tertentu perlu label, layout, validasi, relasi, visibility, atau renderer khusus.

### Referensi
`addField(string|array $name, string $type = 'varchar'): static`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$name` | <code>string&#124;array</code> | Ya | - | Nama field virtual atau array asosiatif `field => type`. |
| `$type` | `string` | Tidak | `'varchar'` | Tipe field, tipe join, tipe renderer, atau tipe aksi sesuai metode. |

### Return
`static`

Mengembalikan instance controller saat ini, sehingga dapat dirangkai dengan method Core lain sebelum `render()`.

### Perilaku
`addField()` memperbarui metadata field yang dibaca renderer tabel, read, form, API, dan dokumen. Efeknya muncul saat `render()` melakukan serialisasi dan menyiapkan response.

> [!NOTE]
>
> Field virtual berguna untuk tampilan atau data schema vertikal, tetapi bukan kolom fisik kecuali model atau schema menyediakan penyimpanannya.

### Contoh Dasar
```php
$this->addField('nama_tampilan', 'varchar');

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->addField('nama_tampilan', 'varchar');
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
            ->addField('nama_tampilan', 'varchar');

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
* [setValidation](./setValidation)
* [fieldOrder](./fieldOrder)
* [unsetField](./unsetField)
* [verticalSchema](./verticalSchema)
