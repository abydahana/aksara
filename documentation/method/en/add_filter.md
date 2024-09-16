Metode ini untuk menambahkan suatu kolom filter pada formulir pencarian pada tabel

### Reference
`add_filter($filter, $options)`

**Parameter**
* **$filter** [`array`|`string`] *filter yang akan ditambahkan sebagai tambahan formulir pencarian;*
* **$options** [`array`] *opsi fallback yang akan diubah menjadi pilihan dropdown / value input.*

&nbsp;

### Usage Sample

```php
$this->add_filter('language', [
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
$this->add_filter([
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

### Read Also
* [add_button](./add_button)
* [add_dropdown](./add_dropdown)
* [add_toolbar](./add_toolbar)
