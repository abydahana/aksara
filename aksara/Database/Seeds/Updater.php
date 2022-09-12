<?php
/**
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @copyright		(c) 2021 - Aksara Laboratory
 * @since			version 4.2.8
 */

namespace Aksara\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Updater extends Seeder
{
    public function run()
    {
		// remove existing privileges
		$this->db->table('app_groups__privileges')->where('path', 'administrative/activities')->delete();
		
		// add new privileges
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/logs","privileges":"[\"index\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/logs\/activities","privileges":"[\"index\",\"read\",\"truncate\",\"delete\",\"print\",\"pdf\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		$this->db->table('app__groups_privileges')->insert(json_decode('{"path":"administrative\/logs\/errors","privileges":"[\"index\",\"remove\",\"clear\"]","last_generated":"' . date('Y-m-d H:i:s') . '"}', true));
		
		// update the group permissions
		$groups										= $this->db->table('app__groups')->where('group_id', 1)->getWhere
		(
			array
			(
				'status'							=> 1
			)
		)
		->getResult();
		
		if($groups)
		{
			foreach($groups as $key => $val)
			{
				// extract privileges
				$privileges = json_decode($val->group_privileges, true);
				
				// push new permissions
				$privileges['administrative/logs'] = array('index');
				$privileges['administrative/logs/activities'] = array('index', 'read', 'truncate', 'delete', 'print', 'pdf');
				$privileges['administrative/logs/errors'] = array('index', 'remove', 'clear');
				
				// update group table
				$this->db->table('app__groups')->where('group_id', $val->group_id)->update
				(
					array
					(
						'group_privileges'			=> json_encode($privileges)
					)
				);
			}
		}
	}
}
