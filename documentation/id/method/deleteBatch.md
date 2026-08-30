`deleteBatch()` menghapus row yang dikirim secara batch melalui pipeline CRUD Core.

### Tujuan
`deleteBatch()` menghapus row yang dikirim secara batch melalui pipeline CRUD Core. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan untuk modul lanjutan yang perlu menyentuh pipeline render, serialisasi, validasi, atau CRUD Core secara langsung. Modul biasa umumnya cukup memanggil `render()`.

### Referensi
`deleteBatch(?string $table = null): object|null`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$table` | `?string` | Tidak | `null` | Nama tabel, dapat memakai alias jika metode mendukungnya. |

### Return
`object|null`

Mengembalikan response Aksara yang menjelaskan hasil delete batch, atau `null` bila alur tidak memiliki response eksplisit.

### Perilaku
`deleteBatch()` merupakan bagian dari pipeline internal Core dan bekerja dengan metadata field, state request, renderer, validasi, hook, serta response CRUD.

### Contoh Dasar
```php
$result = $this->deleteBatch('orders');
```

### Contoh Lanjutan
```php
$result = $this->deleteBatch('orders');

return $result;
```

### Contoh Lengkap
```php
namespace Modules\Pesanan\Controllers;

use Aksara\Laboratory\Core;

class Pesanan extends Core
{
    public function proses()
    {
        $result = $this->deleteBatch('orders');

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
* [deleteData](./deleteData)
* [beforeDelete](./beforeDelete)
* [afterDelete](./afterDelete)
