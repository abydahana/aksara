`fromSubquery()` menggunakan subquery sebagai sumber query.

### Tujuan
`fromSubquery()` menggunakan subquery sebagai sumber query. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan ketika controller perlu membentuk dataset sebelum `render()` mengompilasi query akhir, tanpa keluar dari pipeline CRUD dan response Core.

### Referensi
`fromSubquery(object|string $subquery, string $alias): static`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$subquery` | `object\|string` | Ya | - | Object query builder atau string SQL subquery. |
| `$alias` | `string` | Ya | - | Alias untuk ekspresi select atau subquery. |

### Return
`static`

Mengembalikan instance controller saat ini, sehingga dapat dirangkai dengan method Core lain sebelum `render()`.

### Perilaku
`fromSubquery()` menyimpan instruksi query di state persiapan Core. Instruksi diterapkan saat `render()` membangun query akhir; pemanggilan metode ini saja tidak mengeksekusi query.

### Contoh Dasar
```php
$activeCustomers = $this->model->builder('customers')
    ->select('customer_id, customer_name')
    ->where('is_active', 1);

$this->fromSubquery($activeCustomers, 'customers');

return $this->render('orders');
```

### Contoh Lanjutan
```php
$activeCustomers = $this->model->builder('customers')
    ->select('customer_id, customer_name')
    ->where('is_active', 1);

$this->fromSubquery($activeCustomers, 'customers');
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
        $activeCustomers = $this->model->builder('customers')
            ->select('customer_id, customer_name')
            ->where('is_active', 1);

        $this->fromSubquery($activeCustomers, 'customers');

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
