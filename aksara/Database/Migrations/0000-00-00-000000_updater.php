<?php
/**
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @copyright		(c) 2021 - Aksara Laboratory
 * @since			version 4.2.8
 */

namespace Aksara\Database\Migrations;

use CodeIgniter\Database\Migration;

class Updater extends Migration
{
    public function up()
    {
		// modify or create new table properties
		
		$this->forge->addField
		(
			array
			(
				'user_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'null' => false
				),
				'post_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'null' => false
				),
				'post_type' => array
				(
					'type' => 'varchar',
					'constraint' => 20,
					'null' => false
				),
				'timestamp' => array
				(
					'type' => 'timestamp',
					'null' => false
				)
			)
		);
		$this->forge->addKey('user_id', false, false);
		$this->forge->addKey('post_id', false, false);
		$this->forge->addForeignKey('user_id', 'app__users', 'user_id', ($this->db->DBDriver == 'SQLSRV' ? 'NO ACTION' : 'CASCADE'), ($this->db->DBDriver == 'SQLSRV' ? 'NO ACTION' : 'RESTRICT'));
		$this->forge->createTable('post__likes');
    }
	
	public function down()
	{
		// drop unused tables properties
	}
}
