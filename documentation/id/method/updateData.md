`updateData()` memperbarui satu row melalui pipeline CRUD Core.

### Tujuan
`updateData()` memperbarui satu row melalui pipeline CRUD Core. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan untuk modul lanjutan yang perlu menyentuh pipeline render, serialisasi, validasi, atau CRUD Core secara langsung. Modul biasa umumnya cukup memanggil `render()`.

### Referensi
`updateData(?string $table = null, array $data = [], array $where = []): object|bool`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$table` | `?string` | Tidak | `null` | Nama tabel, dapat memakai alias jika metode mendukungnya. |
| `$data` | `array` | Tidak | `[]` | Data row, kumpulan row, atau payload renderer yang diproses. |
| `$where` | `array` | Tidak | `[]` | Kondisi WHERE tambahan. |

### Return
`object|bool`

Mengembalikan response Aksara saat sukses atau gagal, atau `false` bila alur update tidak menghasilkan response.

### Perilaku
`updateData()` merupakan bagian dari pipeline internal Core dan bekerja dengan metadata field, state request, renderer, validasi, hook, serta response CRUD.

> [!WARNING]
> Selalu gunakan kondisi `$where` yang sempit saat memanggil `updateData()` langsung. Kondisi kosong atau terlalu luas bisa memperbarui record yang salah.

### Contoh Dasar
```php
$result = $this->updateData('orders', ['status' => 'lunas'], ['order_id' => 10]);
```

### Contoh Lanjutan
```php
$result = $this->updateData('orders', ['status' => 'lunas'], ['order_id' => 10]);

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
        $result = $this->updateData('orders', ['status' => 'lunas'], ['order_id' => 10]);

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
* [beforeUpdate](./beforeUpdate)
* [afterUpdate](./afterUpdate)
* [permitUpsert](./permitUpsert)
* [validateForm](./validateForm)
