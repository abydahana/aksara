Sesuai dengan namanya, metode digunakan untuk mewakili `query builder` yang mana berfungsi untuk mengelompokkan suatu hasil dari query database berdasarkan parameter yang diberikan.

### Reference
`groupBy($groupBy)`

**Parameter**
* **$groupBy** [`mixed`] *nama field yang akan dilakukan pengelompokan.*

&nbsp;

### Usage Sample
```php
$this->groupBy('user_id, product_id');
```
