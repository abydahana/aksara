Metode ini untuk menambahkan suatu kolom filter pada formulir pencarian pada tabel

###### Referensi

`add_filter($filter, $options)`

###### Parameter
* **$filter** (array|string) filter yang akan ditambahkan sebagai tambahan formulir pencarian.
* **$options** (array) opsi fallback yang akan diubah menjadi pilihan dropdown / value input

###### Contoh Penggunaan #1

```php
$this->add_filter('language', [
    [
        'id' => 0,
        'label' => phrase('All languages')
    ], [
        'id' => 1,
        'label' => 'English'
    ],[
        'id' => 2,
        'label' => 'Bahasa Indonesia'
    ]
]);
```

###### Contoh Penggunaan #2

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
            ],[
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
            ],[
                'id' => 2,
                'label' => phrase('Foods')
            ]
        ]
    ]
]);
```
