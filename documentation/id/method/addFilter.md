Metode ini untuk menambahkan suatu kolom filter pada formulir pencarian pada tabel

### Referensi
`addFilter($filter, $options)`

**Parameter**
* **$filter** [`array`|`string`] *filter yang akan ditambahkan sebagai tambahan formulir pencarian;*
* **$options** [`array`] *opsi fallback yang akan diubah menjadi pilihan dropdown / value input.*

&nbsp;

### Contoh Penggunaan

```php
$this->addFilter('language', [
    [
        'id' => 0,
        'label' => phrase('All languages')
    ], [
        'id' => 1,
        'label' => 'English'
    ], [
        'id' => 2,
        'label' => 'Bahasa Indonesia'
    ]
]);
```

**Anda juga dapat menggunakan metode berikut secara berkelompok seperti berikut:**

```php
$this->addFilter([
    'language' => [
        'label' => phrase('Language'),
        'values' => [
            [
                'id' => 0,
                'label' => phrase('All languages')
            ], [
                'id' => 1,
                'label' => 'English'
            ], [
                'id' => 2,
                'label' => 'Bahasa Indonesia'
            ]
        ]
    ],
    'category' => [
        'label' => phrase('Category'),
        'values' => [
            [
                'id' => 0,
                'label' => phrase('Uncategorized')
            ], [
                'id' => 1,
                'label' => phrase('Sports')
            ], [
                'id' => 2,
                'label' => phrase('Foods')
            ]
        ]
    ]
]);
```

&nbsp;

### Baca Juga
* [addButton](./addButton)
* [addDropdown](./addDropdown)
* [addToolbar](./addToolbar)
