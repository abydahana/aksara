`insertId()` mengambil ID insert terakhir yang disimpan pipeline Core.

### Tujuan
`insertId()` mengambil ID insert terakhir yang disimpan pipeline Core. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan untuk modul lanjutan yang perlu menyentuh pipeline render, serialisasi, validasi, atau CRUD Core secara langsung. Modul biasa umumnya cukup memanggil `render()`.

### Referensi
`insertId(): int`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| Tidak ada | - | - | - | Metode ini tidak menerima parameter. |

### Return
`int`

Mengembalikan nilai integer dari state Core.

### Perilaku
`insertId()` merupakan bagian dari pipeline internal Core dan bekerja dengan metadata field, state request, renderer, validasi, hook, serta response CRUD.

### Contoh Dasar
```php
$orderId = $this->insertId();
```

### Contoh Lanjutan
```php
$this->insertData('orders', ['status' => 'lunas']);

$orderId = $this->insertId();
return $this->setOutput('order_id', $orderId)->render();
```

### Contoh Lengkap
```php
namespace Modules\Pesanan\Controllers;

use Aksara\Laboratory\Core;

class Pesanan extends Core
{
    public function proses()
    {
        $this->insertData('orders', ['status' => 'lunas']);

        $orderId = $this->insertId();
        return $this->setOutput('order_id', $orderId)->render();
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
* [render](./render)
* [renderTable](./renderTable)
* [renderRead](./renderRead)
* [renderForm](./renderForm)
* [serialize](./serialize)
* [serializeRow](./serializeRow)
* [validateForm](./validateForm)
* [insertData](./insertData)
