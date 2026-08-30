`validateForm()` memvalidasi data form dan menyiapkannya untuk alur create/update.

### Tujuan
`validateForm()` memvalidasi data form dan menyiapkannya untuk alur create/update. Metode ini menjaga kustomisasi modul tetap berada di alur controller Core bawaan.

### Kapan Digunakan
Gunakan untuk modul lanjutan yang perlu menyentuh pipeline render, serialisasi, validasi, atau CRUD Core secara langsung. Modul biasa umumnya cukup memanggil `render()`.

### Referensi
`validateForm(array|object $data)`

### Parameter
| Parameter | Tipe | Wajib | Default | Keterangan |
|---|---|---:|---|---|
| `$data` | `array\|object` | Ya | - | Row sumber yang digunakan untuk validasi dan persiapan data submit. |

### Return
`mixed`

Mengembalikan response Aksara ketika validasi gagal atau alur create/update selesai. Dapat tidak mengembalikan nilai eksplisit ketika Core melanjutkan proses internal.

### Perilaku
`validateForm()` merupakan bagian dari pipeline internal Core dan bekerja dengan metadata field, state request, renderer, validasi, hook, serta response CRUD.

### Contoh Dasar
```php
$order = $this->model->getWhere('orders', ['order_id' => 10], 1)->row();
$validation = $this->validateForm($order);
```

### Contoh Lanjutan
```php
$order = $this->model->getWhere('orders', ['order_id' => 10], 1)->row();
$this->setValidation('status', 'required|in_list[draft,lunas,batal]');

return $this->validateForm($order);
```

### Contoh Lengkap
```php
namespace Modules\Pesanan\Controllers;

use Aksara\Laboratory\Core;

class Pesanan extends Core
{
    public function proses()
    {
        $order = $this->model->getWhere('orders', ['order_id' => 10], 1)->row();
        $this->setValidation('status', 'required|in_list[draft,lunas,batal]');

        return $this->validateForm($order);
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
* [insertData](./insertData)
* [updateData](./updateData)
