This method enables you to set values for inserts or updates.

Example :
```$this->set_default('created_by', get_userdata('user_id'));```
or
```$data = [
            'created_by' => get_userdata('user_id'),
            'updated_by'  => get_userdata('user_id'),
        ];
$this->set_default($data);```
