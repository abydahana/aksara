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
		$this->down();
		
		$this->forge->addField
		(
			array
			(
				'comment_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'auto_increment' => true,
					'null' => false
				),
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
				'reply_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'null' => false
				),
				'mention_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'null' => false
				),
				'comments' => array
				(
					'type' => 'text',
					'null' => false
				),
				'attachment' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'edited' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				),
				'timestamp' => array
				(
					'type' => 'timestamp',
					'null' => false
				),
				'status' => array
				(
					'type' => 'tinyint',
					'constraint' => 1,
					'default' => '0',
					'null' => false
				)
			)
		);
		$this->forge->addKey('comment_id', true, true);
		$this->forge->addKey('user_id', false, false);
		$this->forge->addKey('post_id', false, false);
		$this->forge->addKey('reply_id', false, false);
		$this->forge->addKey('mention_id', false, false);
		$this->forge->addForeignKey('user_id', 'app__users', 'user_id', ($this->db->DBDriver == 'SQLSRV' ? 'NO ACTION' : 'CASCADE'), ($this->db->DBDriver == 'SQLSRV' ? 'NO ACTION' : 'RESTRICT'));
		$this->forge->createTable('post__comments');
		
		$this->forge->addField
		(
			array
			(
				'comment_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'null' => false
				),
				'comments' => array
				(
					'type' => 'text',
					'null' => false
				),
				'attachment' => array
				(
					'type' => 'varchar',
					'constraint' => 255,
					'null' => false
				),
				'timestamp' => array
				(
					'type' => 'timestamp',
					'null' => false
				)
			)
		);
		$this->forge->addKey('comment_id', false, false);
		$this->forge->addForeignKey('comment_id', 'post__comments', 'comment_id', ($this->db->DBDriver == 'SQLSRV' ? 'NO ACTION' : 'CASCADE'), ($this->db->DBDriver == 'SQLSRV' ? 'NO ACTION' : 'RESTRICT'));
		$this->forge->createTable('post__comments_updates');
		
		$this->forge->addField
		(
			array
			(
				'comment_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'null' => false
				),
				'user_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'null' => false
				),
				'timestamp' => array
				(
					'type' => 'timestamp',
					'null' => false
				)
			)
		);
		$this->forge->addKey('comment_id', false, false);
		$this->forge->addKey('user_id', false, false);
		$this->forge->addForeignKey('comment_id', 'post__comments', 'comment_id', ($this->db->DBDriver == 'SQLSRV' ? 'NO ACTION' : 'CASCADE'), ($this->db->DBDriver == 'SQLSRV' ? 'NO ACTION' : 'RESTRICT'));
		$this->forge->addForeignKey('user_id', 'app__users', 'user_id', ($this->db->DBDriver == 'SQLSRV' ? 'NO ACTION' : 'CASCADE'), ($this->db->DBDriver == 'SQLSRV' ? 'NO ACTION' : 'RESTRICT'));
		$this->forge->createTable('post__comments_likes');
		
		$this->forge->addField
		(
			array
			(
				'comment_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'null' => false
				),
				'user_id' => array
				(
					'type' => 'bigint',
					'constraint' => 22,
					'unsigned' => true,
					'null' => false
				),
				'message' => array
				(
					'type' => 'text',
					'null' => false
				),
				'timestamp' => array
				(
					'type' => 'timestamp',
					'null' => false
				)
			)
		);
		$this->forge->addKey('comment_id', false, false);
		$this->forge->addKey('user_id', false, false);
		$this->forge->addForeignKey('comment_id', 'post__comments', 'comment_id', ($this->db->DBDriver == 'SQLSRV' ? 'NO ACTION' : 'CASCADE'), ($this->db->DBDriver == 'SQLSRV' ? 'NO ACTION' : 'RESTRICT'));
		$this->forge->addForeignKey('user_id', 'app__users', 'user_id', ($this->db->DBDriver == 'SQLSRV' ? 'NO ACTION' : 'CASCADE'), ($this->db->DBDriver == 'SQLSRV' ? 'NO ACTION' : 'RESTRICT'));
		$this->forge->createTable('post__comments_reports');
		
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
		$this->forge->addKey('comment_id', false, false);
		$this->forge->addKey('user_id', false, false);
		$this->forge->addForeignKey('user_id', 'app__users', 'user_id', ($this->db->DBDriver == 'SQLSRV' ? 'NO ACTION' : 'CASCADE'), ($this->db->DBDriver == 'SQLSRV' ? 'NO ACTION' : 'RESTRICT'));
		$this->forge->createTable('post__likes');
    }
	
	public function down()
	{
		// drop unused tables properties
		$this->forge->dropTable('comments__likes', true);
		$this->forge->dropTable('comments__reports', true);
		$this->forge->dropTable('comments__updates', true);
		$this->forge->dropTable('comments', true);
	}
}
