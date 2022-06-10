### set_default

Fungsi ini digunakan untuk membuat default value saat proses insert dan update

**Example :**

```php
$this->set_default('created_by', get_userdata('user_id'));
```
or 

```php
$data = [
'created_by' => get_userdata('user_id'),
'updated_by'  => get_userdata('user_id')
];
$this->set_default($data);
```