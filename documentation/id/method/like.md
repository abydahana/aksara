`like()` menambahkan kondisi AND LIKE.

### Tujuan
`like()` menambahkan kondisi AND LIKE. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan ketika controller perlu membentuk dataset sebelum `render()` mengompilasi query akhir, tanpa keluar dari pipeline CRUD dan response Core.

### Referensi
`like(string|array $field = [], mixed $match = '', string $side = 'both', bool $escape = true, bool $caseInsensitive = true): static`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$field` | `string\|array` | Tidak | `[]` | Nama field, daftar field dipisah koma, atau map field asosiatif. |
| `$match` | `mixed` | Tidak | `''` | Teks atau nilai untuk kondisi LIKE/HAVING LIKE. |
| `$side` | `string` | Tidak | `'both'` | Sisi wildcard LIKE: `before`, `after`, `both`, atau nilai yang didukung database layer. |
| `$escape` | `bool` | Tidak | `true` | Menentukan apakah layer database melakukan escaping identifier dan nilai. |
| `$caseInsensitive` | `bool` | Tidak | `true` | Menentukan apakah pencarian mengabaikan kapitalisasi jika didukung. |

### Return
`static`

Mengembalikan instance controller saat ini, sehingga dapat dirangkai dengan method Core lain sebelum `render()`.

### Perilaku
`like()` menyimpan instruksi query di state persiapan Core. Instruksi diterapkan saat `render()` membangun query akhir; pemanggilan metode ini saja tidak mengeksekusi query.

### Contoh Dasar
```php
$this->like('orders.order_number', 'INV');

return $this->render('orders');
```

### Contoh Lanjutan
```php
$this->like('orders.order_number', 'INV');
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
            ->like('orders.order_number', 'INV');

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
* [join](./join)
* [where](./where)
* [orWhere](./orWhere)
* [whereIn](./whereIn)
* [groupBy](./groupBy)
* [having](./having)
* [orderBy](./orderBy)
