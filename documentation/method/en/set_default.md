This method enables you to set values for inserts or updates.

Example :
<br />
```$this->set_default('created_by', get_userdata('user_id'));```
<br />
or
<br />
```
$data = [
'created_by' => get_userdata('user_id'),
'updated_by'  => get_userdata('user_id')
];
$this->set_default($data);
```
