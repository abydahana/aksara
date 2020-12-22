Metode ini untuk menambahkan suatu kolom filter pada formulir pencarian pada tabel

###### Referensi

`add_filter($filter)`

###### Parameter
* **$filter** (string) filter yang akan ditambahkan dalam formulir pencarian.

###### Contoh Penggunaan

```php
$this->add_filter
('
	<select name="jenis_kelamin" class="form-control form-control-sm">
		<option value="1">Pria</option>
		<option value="2">Wanita</option>
	</select>
');
```
