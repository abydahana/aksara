`deleteData()` adalah Core method yang tersedia di dalam controller Aksara.

### Tujuan
`deleteData()` menghapus satu row melalui pipeline CRUD Core. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan untuk modul lanjutan yang perlu menyentuh pipeline render, serialisasi, validasi, atau CRUD Core secara langsung. Modul biasa umumnya cukup memanggil `render()`.

### Referensi
`deleteData(?string $table = null, array $where = [], int $limit = 1): object|null`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$table` | `?string` | Tidak | `null` | Nama tabel, dapat memakai alias jika metode mendukungnya. |
| `$where` | `array` | Tidak | `[]` | Kondisi WHERE tambahan. |
| `$limit` | `int` | Tidak | `1` | Jumlah maksimum row yang dihapus; default satu untuk keamanan. |

### Nilai Kembali
`object|null`

Mengembalikan response Aksara saat sukses atau gagal, atau `null` bila alur tidak memiliki response eksplisit.

### Perilaku
`deleteData()` merupakan bagian dari pipeline internal Core dan bekerja dengan metadata field, state request, renderer, validasi, hook, serta response CRUD.

### Contoh Dasar
```php
$result = $this->deleteData('orders', ['order_id' => 10]);
```

### Contoh Lanjutan
```php
$result = $this->deleteData('orders', ['order_id' => 10]);

return $result;
```

### Contoh Lengkap
```php
namespace Modules\Pesanan\Controllers;

use Aksara\Controllers\BaseController;

class Pesanan extends BaseController
{
    public function proses()
    {
        $result = $this->deleteData('orders', ['order_id' => 10]);

        return $result;
    }
}
```

### Hasil
Metode mengembalikan payload terstruktur atau response object sesuai signature sambil menjaga validasi, hook, dan response Core.

### Catatan
* Sebagian besar modul cukup memakai `render()`; method pipeline level bawah hanya untuk kebutuhan lanjutan.
* Helper CRUD langsung tetap bergantung pada request state, metadata tabel, validasi, dan hook Core.

### Kesalahan Umum
* Melewati validasi atau hook Core tanpa sengaja lewat penulisan database mentah.
* Menganggap payload internal sebagai HTML final.
* Mengembalikan array mentah dari route yang mengharapkan response Aksara.

### Metode Terkait
* [beforeDelete](./beforeDelete)
* [afterDelete](./afterDelete)
* [deleteBatch](./deleteBatch)
