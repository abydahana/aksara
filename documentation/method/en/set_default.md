### set_default

This method enables you to set values for inserts or updates.

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