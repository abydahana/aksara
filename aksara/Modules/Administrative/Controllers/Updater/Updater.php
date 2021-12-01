<?php

namespace Aksara\Modules\Administrative\Controllers\Updater;

/**
 * Administrative > Updater
 * This module is used to update Aksara.
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.2.7
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Updater extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		$this->set_permission(1);
		$this->set_theme('backend');
	}
	
	/**
	 * Ping upstream
	 */
	public static function ping_upstream($changelog = false)
	{
		if(!function_exists('curl_init') || !function_exists('curl_exec') || !@fsockopen('www.aksaracms.com', 443))
		{
			return false;
		}
		
		$curl										= \Config\Services::curlrequest
		(
			array
			(
				'timeout'							=> 5,
				'http_errors'						=> false
			)
		);
		
		$response									= $curl->post
		(
			'https://www.aksaracms.com/updater/ping',
			array
			(
				'allow_redirects'					=> array
				(
					'max'							=> 2
				),
				'headers'							=> array
				(
					'Referer'						=> base_url()
				),
				'form_params'						=> array
				(
					'version'						=> aksara('version'),
					'built_version'					=> aksara('built_version'),
					'changelog'						=> $changelog
				)
			)
		);
		
		return json_decode($response->getBody());
	}
	
	public function index()
	{
		if($this->valid_token(service('request')->getPost('_token')))
		{
			if(DEMO_MODE)
			{
				return throw_exception(403, phrase('changes_will_not_saved_in_demo_mode'), current_page());
			}
			
			if(!function_exists('curl_init') || !function_exists('curl_exec') || !@fsockopen('www.aksaracms.com', 443))
			{
				return false;
			}
			
			$curl									= \Config\Services::curlrequest
			(
				array
				(
					'timeout'						=> 5,
					'http_errors'					=> false
				)
			);
			
			$response								= $curl->post
			(
				'https://www.aksaracms.com/updater/update',
				array
				(
					'allow_redirects'				=> array
					(
						'max'						=> 2
					),
					'headers'						=> array
					(
						'Referer'					=> base_url()
					),
					'form_params'					=> array
					(
						'version'					=> aksara('version'),
						'built_version'				=> aksara('built_version')
					)
				)
			);
			
			$response								= json_decode($response->getBody());
			
			if($response)
			{
				// run updater
				$this->_run_updater($response);
			}
			
			return throw_exception(404, phrase('no_update_available_at_this_time'), current_page());
		}
		
		$this->set_title(phrase('core_system_updater'))
		->set_icon('mdi mdi-update')
		
		->set_output
		(
			array
			(
				'updater'							=> $this->ping_upstream(true)
			)
		)
		
		->render();
	}
	
	/**
	 * Run instant updater
	 */
	private function _run_updater($response = array())
	{
		$old_dependencies							= json_decode(file_get_contents(ROOTPATH . 'composer.json'), true);
		$backup_name								= '_backup_' . date('Y-m-d_His', time()) . '.zip';
		$zip										= new \ZipArchive();
		
		// create backup package
		if($zip->open(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $backup_name, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true)
		{
			$zip->addFile(ROOTPATH . 'composer.json', 'composer.json');
			$zip->addFile(ROOTPATH . 'composer.lock', 'composer.lock');
			
			$files									= new \AppendIterator();
			$files->append(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(ROOTPATH . 'aksara'), \RecursiveIteratorIterator::LEAVES_ONLY));
			$files->append(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(ROOTPATH . 'public'), \RecursiveIteratorIterator::LEAVES_ONLY));
			$files->append(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(ROOTPATH . 'themes'), \RecursiveIteratorIterator::LEAVES_ONLY));
			
			foreach($files as $key => $val)
			{
				// Skip directories (they would be added automatically)
				if(!$val->isDir())
				{
					// Get real and relative path for current file
					$filePath						= $val->getRealPath();
					$relativePath					= str_replace(ROOTPATH, null, $filePath);
					
					// Add current file to archive
					$zip->addFile($filePath, $relativePath);
				}
			}
			
			$updater_package						= null;
			$updater_name							= sha1(time());
			$updated								= false;
			
			try
			{
				// get update package from remote server
				$updater_package					= file_get_contents($response->updater);
				
				file_put_contents(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $updater_name);
				
				if($zip->open(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $updater_name) === true && $zip->extractTo(ROOTPATH))
				{
					$updated						= true;
				}
			}
			catch(\Throwable $e)
			{
				if($zip->open(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $backup_name) === true && $zip->extractTo(ROOTPATH))
				{
					// some notes
				}
			}
			
			$zip->close();
			
			if($updated)
			{
				$unlink								= false;
				$dependency_updated					= array();
				
				try
				{
					unlink(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $updater_name);
					unlink(WRITEPATH . 'cache' . DIRECTORY_SEPARATOR . $backup_name);
					
					$unlink							= true;
				}
				catch(\Throwable $e)
				{
					// some notes
				}
				
				$new_dependencies					= json_decode(file_get_contents(ROOTPATH . 'composer.json'), true);
				
				if(isset($old_dependencies['require']) && isset($new_dependencies['require']))
				{
					$dependency_updated				= array_diff($old_dependencies['require'], $new_dependencies['require']);
					
					$new_dependencies['require']	= array_unique(array_merge($old_dependencies->require, $new_dependencies['require']));
					
					file_put_contents(ROOTPATH . 'composer.json', json_encode($new_dependencies, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
				}
				
				// database migrations and seeder
				try
				{
					// run the updater migration
					$migration						= \Config\Services::migrations()->setNamespace('Aksara');
					
					// migrate the updater database schema
					if($migration->latest())
					{
						// call seeder
						$seeder						= \Config\Database::seeder();
						
						// run seeder
						$seeder->call('Updater');
					}
				}
				catch(\Throwable $e)
				{
					// some notes
				}
				
				$html								= '
					<div class="text-center mb-3">
						<i class="mdi mdi-arrow-up-circle-outline mdi-5x text-success"></i>
						<br />
						<h5>
							' . phrase('your_core_system_has_been_successfully_updated') . '
						</h5>
					</div>
					' . ($dependency_updated ? '
					<div class="alert alert-warning text-sm border-0 rounded-0 row">
						<div class="text-center">
							' . phrase('you_may_need_to_run_the_composer_update_from_the_directory_below_to_update_the_dependencies') . ':
							<br />
							<code>' . ROOTPATH . '</code>
						</div>
					</div>
					' : null) . '
					' . (!$unlink ? '
					<div class="alert alert-warning text-sm border-0 rounded-0 row">
						<div class="text-center">
							' . phrase('unable_to_remove_the_updater_junk_files_from_the_cache_directory') . ':
							<br />
							<code>' . WRITEPATH . 'cache' . '</code>
						</div>
					</div>
					' : null) . '
					<p class="text-center">
						' . phrase('you_will_be_notified_when_another_update_is_available') . ' ' . phrase('keep_in_mind_that_we_are_collect_the_donation_from_people_like_you_to_support_our_research') . ' ' . phrase('we_look_forward_to_your_contributions_either_kind_of_donations_or_development') . '
					</p>
					<p class="text-center lead">
						<i class="mdi mdi-heart text-danger"></i>
						<a href="//abydahana.github.io" target="_blank">Aby Dahana</a>
					</p>
					<hr class="row" />
					<div class="row">
						<div class="col-6">
							<a href="//www.aksaracms.com/pages/about/donation" class="btn btn-light btn-block" target="_blank">
								<i class="mdi mdi-launch"></i>
								' . phrase('donate') . '
							</a>
						</div>
						<div class="col-6">
							<a href="' . current_page() . '" class="btn btn-primary btn-block">
								<i class="mdi mdi-reload"></i>
								' . phrase('refresh') . '
							</a>
						</div>
					</div>
				';
				
				return make_json
				(
					array
					(
						'status'					=> 200,
						'meta'						=> array
						(
							'title'					=> phrase('update_success'),
							'icon'					=> 'mdi mdi-check',
							'popup'					=> true
						),
						'html'						=> $html
					)
				);
			}
		}
		
		return throw_exception(400, array('upgrade' => phrase('update_canceled_due_to_inability_to_create_the_backup_file')));
	}
}
