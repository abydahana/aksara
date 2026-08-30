`select()` menambahkan kolom ke daftar SELECT.

### Tujuan
`select()` menambahkan kolom ke daftar SELECT. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan ketika controller perlu membentuk dataset sebelum `render()` mengompilasi query akhir, tanpa keluar dari pipeline CRUD dan response Core.

### Referensi
`select(string|array $column, bool $escape = true): static`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$column` | <code>string&#124;array</code> | Ya | - | Nama kolom, ekspresi kolom, daftar kolom dipisah koma, atau array kolom. |
| `$escape` | `bool` | Tidak | `true` | Menentukan apakah layer database melakukan escaping identifier dan nilai. |

### Return
`static`

Mengembalikan instance controller saat ini, sehingga dapat dirangkai dengan method Core lain sebelum `render()`.

### Perilaku
`select()` menyimpan instruksi query di state persiapan Core. Instruksi diterapkan saat `render()` membangun query akhir; pemanggilan metode ini saja tidak mengeksekusi query.

> [!WARNING]
> Biarkan `$escape` aktif kecuali ekspresi SELECT adalah SQL mentah yang sudah dipercaya.

### Contoh Dasar
```php
$this->select('orders.order_id, orders.order_number, orders.status');

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->select('orders.order_id, orders.order_number, orders.status');
$this->join('customers', 'customers.customer_id = orders.customer_id', 'left')
    ->where('orders.deleted_at', null)
    ->orderBy('orders.created_at', 'DESC')
    ->limit(25);

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
            ->select('orders.order_id, orders.order_number, orders.status');

        return $this->render('orders');
    }
}
```

### Hasil
Query akhir menyertakan klausa atau state yang dikonfigurasi sebelum row diserialisasi untuk tabel, dokumen, atau response API.

### Catatan
* Metode ini chainable dan biasanya dipanggil sebelum `render()`.
* Metode ini mengonfigurasi query controller; ini berbeda dari memanggil API model secara langsung.
* Biarkan escaping aktif kecuali ekspresi SQL mentah memang disengaja dan sudah dipercaya.

### Kesalahan Umum
* Memanggil method setelah `render()`, karena query sudah dikompilasi.
* Menonaktifkan escaping untuk input dari request.
* Lupa menutup grup WHERE atau HAVING yang sudah dibuka.

### Metode Terkait
* [selectCount](./selectCount)
* [selectSum](./selectSum)
* [selectMin](./selectMin)
* [selectMax](./selectMax)
* [selectAvg](./selectAvg)
* [selectSubquery](./selectSubquery)
* [unsetSelect](./unsetSelect)
* [distinct](./distinct)
