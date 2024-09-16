Sesuai dengan namanya, metode digunakan untuk mewakili `query builder` yang mana berfungsi untuk mengelompokkan suatu hasil dari query database berdasarkan parameter yang diberikan.

### Reference
`group_by($group_by)`

**Parameter**
* **$group_by** [`mixed`] *nama field yang akan dilakukan pengelompokan.*

&nbsp;

### Usage Sample
```php
$this->group_by('user_id, product_id');
```
