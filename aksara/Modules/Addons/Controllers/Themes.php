<?php namespace Aksara\Modules\Addons\Controllers;
/**
 * Addons > Themes Manager
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */
class Themes extends \Aksara\Laboratory\Core
{
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
		$this->set_title(phrase('theme_manager'))
		->set_icon('mdi mdi-palette')
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
	 * Get theme detail
	 */
	public function detail()
	{
		$package									= json_decode(file_get_contents(ROOTPATH . 'themes/' . $this->_primary . '/package.json'));
		
		if($package)
		{
			$package->folder						= $this->_primary;
			$package->integrity						= sha1($package->folder . ENCRYPTION_KEY . get_userdata('session_generated'));
		}
		
		$this->set_title(phrase('theme_detail'))
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
	 * Activate theme
	 */
	public function activate()
	{
		if(DEMO_MODE)
		{
			return throw_exception(404, phrase('changes_will_not_saved_in_demo_mode'), current_page());
		}
		
	}
	
	/**
	 * Customize module
	 */
	public function customize()
	{
		if(!file_exists(ROOTPATH . 'themes/' . service('request')->getGet('theme') . '/package.json'))
		{
			return throw_exception(404, phrase('no_theme_package_manifest_were_found'), current_page('../'));
		}
		
		$package									= json_decode(file_get_contents(ROOTPATH . 'themes/' . service('request')->getGet('theme') . '/package.json'));
		
		if(!$package)
		{
			return throw_exception(403, phrase('no_theme_package_manifest_were_found'), current_page('../'));
		}
		
		$package->folder							= service('request')->getGet('theme');
		$package->integrity							= sha1($package->folder . ENCRYPTION_KEY . get_userdata('session_generated'));
		
		if($this->valid_token(service('request')->getPost('_token')))
		{
			if(DEMO_MODE)
			{
				return throw_exception(404, phrase('changes_will_not_saved_in_demo_mode'), current_page());
			}
			elseif(!is_writable(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $package->folder . DIRECTORY_SEPARATOR . 'package.json'))
			{
				return throw_exception(400, array('colorscheme' => ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $package->folder . DIRECTORY_SEPARATOR . 'package.json ' . phrase('is_not_writable')));
			}
			
			$package->colorscheme					= service('request')->getPost('colorscheme');
			$folder									= $package->folder;
			
			unset($package->folder, $package->integrity);
			
			if(file_put_contents(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . 'package.json', json_encode($package)))
			{
				return throw_exception(301, phrase('the_theme_was_successfully_customized'), current_page());
			}
			
			return throw_exception(400, array('colorscheme' => ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . 'package.json ' . phrase('is_not_writable')));
		}
		
		$this->set_title(phrase('theme_customization'))
		->set_icon('mdi mdi-palette')
		->set_output
		(
			array
			(
				'writable'							=> (is_writable(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $package->folder . DIRECTORY_SEPARATOR . 'package.json') ? true : false),
				'detail'							=> $package
			)
		)
		->modal_size('modal-lg')
		
		->render();
	}
	
	/**
	 * Delete theme
	 */
	public function delete()
	{
		$this->permission->must_ajax(current_page('../'));
		
		/* check if theme is exists */
		if($this->_primary && !is_dir(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $this->_primary))
		{
			return throw_exception(404, phrase('the_theme_you_would_to_delete_is_not_exists_or_already_removed'), curent_page('../'));
		}
		
		/* delete confirmation */
		elseif($this->_primary && $this->_primary != service('request')->getPost('theme'))
		{
			$html									= '
				<div class="p-3">
					<form action="' . current_page() . '" method="POST" class="--validate-form">
						<div class="text-center">
							' . phrase('are_you_sure_want_to_delete_this_theme') . '
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
								<input type="hidden" name="theme" value="' . $this->_primary . '" />
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
		
		/* check if requested theme to delete is match */
		if(service('request')->getPost('theme') && is_dir(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . service('request')->getPost('theme')))
		{
			if(DEMO_MODE)
			{
				return throw_exception(404, phrase('changes_will_not_saved_in_demo_mode'), current_page());
			}
			
			/* check if theme property is exists */
			if(file_exists(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . service('request')->getPost('theme') . DIRECTORY_SEPARATOR . 'package.json'))
			{
				$package							= json_decode(file_get_contents(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . service('request')->getPost('theme') . DIRECTORY_SEPARATOR . 'package.json'));
				
				if(isset($package->type) && 'backend' == $package->type)
				{
					$type							= 'backend_theme';
				}
				else
				{
					$type							= 'frontend_theme';
				}
				
				if(service('request')->getPost('theme') == get_setting($type))
				{
					return throw_exception(403, phrase('unable_to_delete_the_theme_that_are_in_use'), current_page('../'));
				}
				
				/* delete theme */
				helper('filesystem');
				
				if(!delete_files(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . service('request')->getPost('theme'), true) || (is_dir(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . service('request')->getPost('theme')) && !@rmdir(ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . service('request')->getPost('theme'))))
				{
					/* Unable to delete theme. Get FTP configuration */
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
							/* yay! FTP is connected, try to delete theme */
							$this->_ftp_rmdir($connection, ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . service('request')->getPost('theme'));
							
							/* close FTP connection */
							ftp_close($connection);
						}
					}
					
					/* uh oh! still unable to delete theme */
					return throw_exception(403, phrase('unable_to_delete_the_selected_theme_due_to_folder_permission'), current_page('../'));
				}
			}
			else
			{
				/* theme property is not found */
				return throw_exception(404, phrase('a_theme_without_package_cannot_be_removed_from_theme_manager'), current_page('../'));
			}
		}
		
		return throw_exception(301, phrase('the_selected_theme_was_successfully_removed'), current_page('../'));
	}
	
	/**
	 * List installed themes
	 */
	private function _installed()
	{
		/* load required helper */
		helper('filesystem');
		
		$data										= directory_map(ROOTPATH . 'themes');
		
		if(!$data) return false;
		
		$output										= array();
		
		foreach($data as $key => $val)
		{
			if(is_array($val))
			{
				foreach($val as $_key => $_val)
				{
					if($_val != 'package.json') continue;
					
					$package						= json_decode(file_get_contents(ROOTPATH . 'themes/' . $key . $_val));
					
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
