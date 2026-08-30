`offset()` mengatur offset hasil default.

### Tujuan
`offset()` mengatur offset hasil default. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan ketika controller perlu membentuk dataset sebelum `render()` mengompilasi query akhir, tanpa keluar dari pipeline CRUD dan response Core.

### Referensi
`offset(int $offset): static`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$offset` | `int` | Ya | - | Jumlah row yang dilewati. |

### Return
`static`

Mengembalikan instance controller saat ini, sehingga dapat dirangkai dengan method Core lain sebelum `render()`.

### Perilaku
`offset()` menyimpan instruksi query di state persiapan Core. Instruksi diterapkan saat `render()` membangun query akhir; pemanggilan metode ini saja tidak mengeksekusi query.

### Contoh Dasar
```php
$this->offset(50);

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->offset(50);
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
            ->offset(50);

        return $this->render('orders');
    }
}
```

### Hasil
Query akhir menyertakan klausa atau state yang dikonfigurasi sebelum row diserialisasi untuk tabel, dokumen, atau response API.

### Catatan
* Metode ini chainable dan biasanya dipanggil sebelum `render()`.
* Metode ini mengonfigurasi query controller; ini berbeda dari memanggil API model secara langsung.

### Kesalahan Umum
* Memanggil method setelah `render()`, karena query sudah dikompilasi.
* Menonaktifkan escaping untuk input dari request.
* Lupa menutup grup WHERE atau HAVING yang sudah dibuka.

### Metode Terkait
* [select](./select)
* [join](./join)
* [where](./where)
* [orWhere](./orWhere)
* [whereIn](./whereIn)
* [like](./like)
* [groupBy](./groupBy)
* [having](./having)
