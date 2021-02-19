<?php namespace Aksara\Laboratory;
/**
 * Seeder
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.2.6
 * @copyright		(c) 2021 - Aksara Laboratory
 */
class Seeder
{
	public function __construct()
	{
	}
	
	public static function seed()
	{
		/**
		 * Create app__years table
		 */
		$dbforge									= \Config\Database::forge();
		
		$dbforge->addField
		(
			array
			(
				'year'								=> array
				(
					'type'							=> 'YEAR',
					'constraint'					=> 4,
					'unsigned'						=> true
				),
				'default'							=> array
				(
					'type'							=> 'TINYINT',
					'constraint'					=> 1
				),
				'status'							=> array
				(
					'type'							=> 'TINYINT',
					'constraint'					=> 1
				)
			)
		);
		
		$dbforge->addPrimaryKey('year');
		
		$dbforge->createTable('app__years', true);
		
		// ----------------------------------------------------------
		
		/**
		 * Create app__connections table
		 */
		$dbforge									= \Config\Database::forge();
		
		$dbforge->addField
		(
			array
			(
				'year'								=> array
				(
					'type'							=> 'YEAR',
					'constraint'					=> 4
				),
				'name'								=> array
				(
					'type'							=> 'VARCHAR',
					'constraint'					=> 255
				),
				'description'						=> array
				(
					'type'							=> 'TINYTEXT'
				),
				'database_driver'					=> array
				(
					'type'							=> 'VARCHAR',
					'constraint'					=> 32
				),
				'hostname'							=> array
				(
					'type'							=> 'VARCHAR',
					'constraint'					=> 255
				),
				'port'								=> array
				(
					'type'							=> 'VARCHAR',
					'constraint'					=> 255
				),
				'username'							=> array
				(
					'type'							=> 'VARCHAR',
					'constraint'					=> 255
				),
				'password'							=> array
				(
					'type'							=> 'VARCHAR',
					'constraint'					=> 255
				),
				'database_name'						=> array
				(
					'type'							=> 'VARCHAR',
					'constraint'					=> 255
				),
				'status'							=> array
				(
					'type'							=> 'TINYINT',
					'constraint'					=> 1
				)
			)
		);
		
		$dbforge->addPrimaryKey(array('year', 'database_driver'));
		
		$dbforge->addForeignKey('year', 'app__years', 'year', 'CASCADE', 'CASCADE');
		
		$dbforge->createTable('app__connections', true);
		
		// ----------------------------------------------------------
		
		/**
		 * Create app__shortlink table
		 */
		$dbforge									= \Config\Database::forge();
		
		$dbforge->addField
		(
			array
			(
				'hash'								=> array
				(
					'type'							=> 'VARCHAR',
					'constraint'					=> 6
				),
				'url'								=> array
				(
					'type'							=> 'VARCHAR',
					'constraint'					=> 255
				),
				'session'							=> array
				(
					'type'							=> 'TEXT'
				)
			)
		);
		
		$dbforge->addPrimaryKey('hash');
		
		$dbforge->createTable('app__shortlink', true);
		
		// ----------------------------------------------------------
	}
}
