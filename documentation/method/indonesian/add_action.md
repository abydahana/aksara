Metode ini dugunakan ketika akan menambahkan tombol atau link pada suatu data yang ditampilkan dalam tabel CRUD. Pada kasus tertentu, Anda mungkin akan membutuhkannya apabila ingin menambahkan tindakan lain terkait dengan primary ID yang diminta, misal untuk menghubungkan ke dalam modul lain, dengan primary ID yang diambil dari referensi tabel CRUD saat ini.

###### Referensi

`add_action($placement, $url, $label, $class, $icon, $parameter, $new_tab)`

###### Parameter

* **$placement** (string) - penempatan posisi tombol, [`toolbar`, `option`, `dropdown`] (default: `option`),
* **$url** (string) - link target,
* **$label** (string) - label untuk tombol,
* **$class** (string) - class CSS untuk jenis tombol,
* **$icon** (string) - ikon untuk tombol (default `mdi mdi-link`),
* **$parameter** (array) - parameter tambahan untuk ditambahkan sebagai query string,
* **$new_tab** (bool) - opsi tab window pada saat membuka link.

###### Contoh Penggunaan

`$this->add_action('toolbar', 'current/pages/import', 'Import Data', 'btn-success --xhr', 'mdi mdi-import', array('id' => 3));`

Pemanggilan metode di atas akan menambah satu tombol pada toolbar yang mengarah pada modul `current/pages/import?id=3`
