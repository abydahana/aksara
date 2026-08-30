`serialize()` menormalisasi kumpulan row database menjadi metadata field Core.

### Tujuan
`serialize()` menormalisasi kumpulan row database menjadi metadata field Core. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan untuk modul lanjutan yang perlu menyentuh pipeline render, serialisasi, validasi, atau CRUD Core secara langsung. Modul biasa umumnya cukup memanggil `render()`.

### Referensi
`serialize(array $data): array`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$data` | `array` | Ya | - | Kumpulan row yang akan dinormalisasi. |

### Return
`array`

Mengembalikan payload Core terstruktur untuk renderer atau response API.

### Perilaku
`serialize()` merupakan bagian dari pipeline internal Core dan bekerja dengan metadata field, state request, renderer, validasi, hook, serta response CRUD.

### Contoh Dasar
```php
$rows = $this->model->get('orders')->result();
$serialized = $this->serialize($rows);
```

### Contoh Lanjutan
```php
$rows = $this->model->get('orders')->result();
$serialized = $this->serialize($rows);

return $this->setOutput('preview', $serialized)->render();
```

### Contoh Lengkap
```php
namespace Modules\Pesanan\Controllers;

use Aksara\Laboratory\Core;

class Pesanan extends Core
{
    public function proses()
    {
        $rows = $this->model->get('orders')->result();
        $serialized = $this->serialize($rows);

        return $this->setOutput('preview', $serialized)->render();
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
* [serializeRow](./serializeRow)
* [validateForm](./validateForm)
* [insertData](./insertData)
* [updateData](./updateData)
