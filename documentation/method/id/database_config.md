Metode ini digunakan apabila ingin menghubungkan sebuah modul untuk menggunakan koneksi database eksternal misalnya untuk keperluan sinkronisasi.

###### Referensi

`database_config($driver, $hostname, $port, $username, $password, $database)`

###### Parameter
* **$driver** (mixed) - jenis driver yang digunakan
* **$hostname** (string) - hostname dari server database
* **$port** (integer) - port dari server database
* **$username** (string) - nama pengguna database
* **$password** (string) - kata sandi database
* **$database** (string) - inisial database yang akan digunakan

###### Contoh Penggunaan

`$this->database_config('sqlsrv', '127.0.0.1', 1433, 'sa', 'MyStrongPassword!', 'master');`

Anda juga dapat menjalankan metode dengan parameter seperti berikut:

```php
$this->database_config
(
	array
	(
		'driver'		=> 'sqlsrv',
		'hostname'		=> '127.0.0.1',
		'port'			=> 1433,
		'username'		=> 'sa',
		'password'		=> 'MyStrongPassword!',
		'database'		=> 'master'
	)
);
```
