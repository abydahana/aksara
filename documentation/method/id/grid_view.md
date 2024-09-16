Metode `grid_view` digunakan untuk membuat tampilan halaman CRUD ke dalam format grid (kotak). Format grid biasanya digunakan untuk mengelola table yang berkaitan dengan gambar.

Contoh untuk tampilan table yang telah diformat dalam tampilan grid dapat ditemukan di bawah modul CMS > Galeri.

### Referensi
`grid_view($thumbnail, $hyperlink, $parameter, $new_tab)`

**Parameter**
* **$thumbnail** [`string`] *kolom dari table yang digunakan sebagai string penyimpan gambar;*
* **$hyperlink** [`string`] *target halaman (slug) yang dituju saat grid diklik;*
* **$parameter** [`array`] *kunci query string yang akan digunakan sebagai primary key;*
* **$new_tab** [`boolean`] *pilihan untuk membuka link pada jendela baru.*

&nbsp;

### Contoh Penggunaan
```php
$this->grid_view('gallery_images', 'galleries', ['gallery_slug' => 'gallery_slug'], true);
```
