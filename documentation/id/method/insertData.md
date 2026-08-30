`insertData()` menyimpan satu row melalui pipeline CRUD Core.

### Tujuan
`insertData()` menyimpan satu row melalui pipeline CRUD Core. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan untuk modul lanjutan yang perlu menyentuh pipeline render, serialisasi, validasi, atau CRUD Core secara langsung. Modul biasa umumnya cukup memanggil `render()`.

### Referensi
`insertData(?string $table = null, array $data = []): object|null`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$table` | `?string` | Tidak | `null` | Nama tabel, dapat memakai alias jika metode mendukungnya. |
| `$data` | `array` | Tidak | `[]` | Data row, kumpulan row, atau payload renderer yang diproses. |

### Return
`object|null`

Mengembalikan response Aksara saat sukses atau gagal, atau `null` bila alur tidak memiliki response eksplisit.

### Perilaku
`insertData()` merupakan bagian dari pipeline internal Core dan bekerja dengan metadata field, state request, renderer, validasi, hook, serta response CRUD.

### Contoh Dasar
```php
$result = $this->insertData('orders', ['status' => 'lunas']);
```

### Contoh Lanjutan
```php
$result = $this->insertData('orders', ['status' => 'lunas']);

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
        $result = $this->insertData('orders', ['status' => 'lunas']);

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
* [beforeInsert](./beforeInsert)
* [afterInsert](./afterInsert)
* [insertId](./insertId)
* [validateForm](./validateForm)
