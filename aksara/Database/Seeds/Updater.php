<?php
/**
 * @author			Aby Dahana
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
		/**
		 * -----------------------------------------------------------
		 * Add new privileges
		 * -----------------------------------------------------------
		 */
		$this->db->table('app__groups_privileges')->updateBatch
		(
			array
			(
				array
				(
					'path' => 'addons',
					'privileges' => '["index","detail","install"]',
					'last_generated' => date('Y-m-d H:i:s')
				),
				array
				(
					'path' => 'addons/modules',
					'privileges' => '["index","detail","import","update","delete"]',
					'last_generated' => date('Y-m-d H:i:s')
				),
				array
				(
					'path' => 'addons/themes',
					'privileges' => '["index","detail","import","update","delete","activate","customize"]',
					'last_generated' => date('Y-m-d H:i:s')
				)
			),
			'path' // where path is on defined
		);
		
		/**
		 * -----------------------------------------------------------
		 * Push a new privileges
		 * -----------------------------------------------------------
		 */
		$groups = $this->db->table('app__groups')->whereIn('group_id', array(1))->getWhere
		(
			array
			(
				'status' => 1
			)
		)
		->getResult();
		
		if($groups)
		{
			foreach($groups as $key => $val)
			{
				$privileges = json_decode($val->group_privileges, true);
				$privileges['addons'] = array('index', 'detail', 'install');
				$privileges['addons/modules'] = array('index', 'detail', 'import', 'update', 'delete');
				$privileges['addons/themes'] = array('index', 'detail', 'import', 'update', 'delete', 'activate', 'customize');
				
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
