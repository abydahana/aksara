<?php namespace Aksara\Modules\Addons\Controllers;
/**
 * Addons > Modules Manager
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
class Modules extends \Aksara\Laboratory\Core
{
	private $_table									= 'app__menus';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->restrict_on_demo();
		
		$this->set_permission(1);
		$this->set_theme('backend');
		
		$this->_primary								= service('request')->getGet('item');
	}
	
	public function index()
	{
		$this->set_title(phrase('module_manager'))
		->set_icon('mdi mdi-puzzle')
		->set_output
		(
			array
			(
				'installed'							=> $this->_installed()
			)
		)
		
		->render();
	}
	
	/**
	 * Get module detail
	 */
	public function detail()
	{
		$package									= json_decode(file_get_contents(ROOTPATH . 'modules/' . service('request')->uri->getSegment(4) . '/package.json'));
		
		if($package)
		{
			$package->folder						= service('request')->uri->getSegment(4);
			$package->integrity						= sha1($package->folder . ENCRYPTION_KEY . get_userdata('session_generated'));
		}
		
		$this->set_title(phrase('module_detail'))
		->set_icon('mdi mdi-palette')
		->set_output
		(
			array
			(
				'detail'							=> $package
			)
		)
		->modal_size('modal-lg')
		
		->render();
	}
	
	/**
	 * Activate module
	 */
	public function activate()
	{
		if(DEMO_MODE)
		{
			return throw_exception(404, phrase('changes_will_not_saved_in_demo_mode'), current_page());
		}
		
	}
	
	/**
	 * Delete module
	 */
	public function delete()
	{
		$this->permission->must_ajax(current_page('../'));
		
		/* check if module is exists */
		if(service('request')->uri->getSegment(4) && !is_dir(ROOTPATH . 'modules/' . service('request')->uri->getSegment(4)))
		{
			return throw_exception(404, phrase('the_theme_you_would_to_delete_is_not_exists_or_already_removed'), curent_page('../'));
		}
		
		/* delete confirmation */
		elseif(service('request')->uri->getSegment(4) && service('request')->uri->getSegment(4) != service('request')->getPost('module'))
		{
			$html									= '
				<div class="p-3">
					<form action="' . current_page() . '" method="POST" class="--validate-form">
						<div class="text-center">
							' . phrase('are_you_sure_want_to_delete_this_module') . '
						</div>
						<hr class="row" />
						<div class="--validation-callback mb-0"></div>
						<div class="row">
							<div class="col-6">
								<a href="javascript:void(0)" data-dismiss="modal" class="btn btn-light btn-block">
									<i class="mdi mdi-window-close"></i>
									' . phrase('cancel') . '
								</a>
							</div>
							<div class="col-6">
								<input type="hidden" name="module" value="' . service('request')->uri->getSegment(4) . '" />
								<button type="submit" class="btn btn-danger btn-block">
									<i class="mdi mdi-check"></i>
									' . phrase('continue') . '
								</button>
							</div>
						</div>
					</form>
				</div>
			';
			
			return make_json
			(
				array
				(
					'status'						=> 200,
					'meta'							=> array
					(
						'title'						=> phrase('action_warning'),
						'icon'						=> 'mdi mdi-alert-outline',
						'popup'						=> true
					),
					'html'							=> $html
				)
			);
		}
		
		/* check if requested module to delete is match */
		if(service('request')->getPost('module') && is_dir(ROOTPATH . 'modules/' . service('request')->getPost('module')))
		{
			if(DEMO_MODE)
			{
				return throw_exception(404, phrase('changes_will_not_saved_in_demo_mode'), current_page());
			}
			
			/* check if module property is exists */
			if(file_exists(ROOTPATH . 'modules/' . service('request')->getPost('module') . '/package.json'))
			{
				$package							= json_decode(file_get_contents(ROOTPATH . 'modules/' . service('request')->getPost('module') . '/package.json'));
				
				if(isset($package->type) && 'backend' == $package->type)
				{
					$type							= 'backend_theme';
				}
				else
				{
					$type							= 'frontend_theme';
				}
				
				if(service('request')->getPost('module') == get_setting($type))
				{
					return throw_exception(403, phrase('unable_to_delete_the_theme_that_are_in_use'), current_page('../'));
				}
				
				/* delete module */
				helper('filesystem');
				
				if(!delete_files(ROOTPATH . 'modules/' . service('request')->getPost('module'), true) || is_dir(ROOTPATH . 'modules/' . service('request')->getPost('module')))
				{
					/* Unable to delete module. Get FTP configuration */
					$site_id						= get_setting('id');
					
					$query							= $this->model->get_where
					(
						'app__ftp',
						array
						(
							'site_id'				=> $site_id
						),
						1
					)
					->row();
					
					if($query)
					{
						/* configuration found, decrypt password */
						$query->password			= service('encrypter')->decrypt(base64_decode($query->password));
						
						/* trying to delete module using ftp instead */
						$connection					= @ftp_connect($query->hostname, $query->port, 10);
						
						if($connection && @ftp_login($connection, $query->username, $query->password))
						{
							/* yay! FTP is connected, try to delete module */
							$this->_ftp_rmdir($connection, ROOTPATH . 'modules/' . service('request')->getPost('module'));
							
							/* close FTP connection */
							ftp_close($connection);
						}
					}
					
					/* uh oh! still unable to delete module */
					return throw_exception(403, phrase('unable_to_delete_the_selected_theme_due_to_folder_permission'), current_page('../'));
				}
			}
			else
			{
				/* module property is not found */
				return throw_exception(404, phrase('a_theme_without_package_cannot_be_removed_from_theme_manager'), current_page('../'));
			}
		}
		
		return throw_exception(301, phrase('the_selected_theme_was_successfully_removed'), current_page('../'));
	}
	
	/**
	 * List installed modules
	 */
	private function _installed()
	{
		/* load required helper */
		helper('filesystem');
		
		$data										= directory_map(ROOTPATH . 'modules');
		
		if(!$data) return false;
		
		$output										= array();
		
		foreach($data as $key => $val)
		{
			if(is_array($val))
			{
				foreach($val as $_key => $_val)
				{
					if($_val != 'package.json') continue;
					
					$package						= json_decode(file_get_contents(ROOTPATH . 'modules/' . $key . $_val));
					
					if($package)
					{
						$package->folder			= str_replace(array('/', '\\'), array(null, null), $key);
						$package->integrity			= sha1($package->folder . ENCRYPTION_KEY . get_userdata('session_generated'));
						
						$output[]					= $package;
					}
				}
			}
		}
		
		return $output;
	}
	
	/**
	 * Remove directory and its files using FTP
	 */
	private function _ftp_rmdir($connection = null, $directory)
	{
		$lists										= ftp_mlsd($connection, $directory);
		
		unset($lists[0]);
		unset($lists[1]);

		foreach($lists as $list)
		{
			$full									= $directory . DIRECTORY_SEPARATOR . $list['name'];
			
			if($list['type'] == 'dir')
			{
				// directory found, reinitialize
				$this->_ftp_rmdir($connection, $full);
			}
			else
			{
				// delete file
				ftp_delete($connection, $full);
			}
		}
		
		// delete directory
		ftp_rmdir($connection, $directory);
	}
}
