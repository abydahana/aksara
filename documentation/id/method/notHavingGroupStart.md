`notHavingGroupStart()` membuka grup kondisi NOT HAVING.

### Tujuan
`notHavingGroupStart()` membuka grup kondisi NOT HAVING. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan ketika controller perlu membentuk dataset sebelum `render()` mengompilasi query akhir, tanpa keluar dari pipeline CRUD dan response Core.

### Referensi
`notHavingGroupStart(): static`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| Tidak ada | - | - | - | Metode ini tidak menerima parameter. |

### Return
`static`

Mengembalikan instance controller saat ini, sehingga dapat dirangkai dengan method Core lain sebelum `render()`.

### Perilaku
`notHavingGroupStart()` menyimpan instruksi query di state persiapan Core. Instruksi diterapkan saat `render()` membangun query akhir; pemanggilan metode ini saja tidak mengeksekusi query. Panggilan pembuka dan penutup grup harus seimbang.

### Contoh Dasar
```php
$this->notHavingGroupStart()->having('total_amount <', 1000)->havingGroupEnd();

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->select('orders.order_id, orders.order_number, orders.status')
    ->groupStart()
        ->where('orders.status', 'lunas')
        ->orWhere('orders.status', 'pending')
    ->groupEnd()
    ->orderBy('orders.created_at', 'DESC');

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
            ->notHavingGroupStart()->having('total_amount <', 1000)->havingGroupEnd();

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
