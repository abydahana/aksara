`join()` adalah Core method yang tersedia di dalam controller Aksara.

### Tujuan
`join()` menambahkan klausa JOIN ke query. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan ketika controller perlu membentuk dataset sebelum `render()` mengompilasi query akhir, tanpa keluar dari pipeline CRUD dan response Core.

### Referensi
`join(string $table, string $condition, string $type = '', bool $escape = true): static`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$table` | `string` | Ya | - | Nama tabel, dapat memakai alias jika metode mendukungnya. |
| `$condition` | `string` | Ya | - | Ekspresi kondisi JOIN. |
| `$type` | `string` | Tidak | `''` | Tipe field, tipe join, tipe renderer, atau tipe action sesuai metode. |
| `$escape` | `bool` | Tidak | `true` | Menentukan apakah layer database melakukan escaping identifier dan nilai. |

### Nilai Kembali
`static`

Mengembalikan instance controller saat ini, sehingga dapat dirangkai dengan method Core lain sebelum `render()`.

### Perilaku
`join()` menyimpan instruksi query di state persiapan Core. Instruksi diterapkan saat `render()` membangun query akhir; pemanggilan metode ini saja tidak mengeksekusi query.

### Contoh Dasar
```php
$this->join('customers', 'customers.customer_id = orders.customer_id', 'left');

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->join('customers', 'customers.customer_id = orders.customer_id', 'left');
$this->join('customers', 'customers.customer_id = orders.customer_id', 'left')
    ->where('orders.deleted_at', null)
    ->orderBy('orders.created_at', 'DESC')
    ->limit(25);

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
            ->join('customers', 'customers.customer_id = orders.customer_id', 'left');

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
* [select](./select)
* [from](./from)
* [where](./where)
* [groupBy](./groupBy)
