<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * XHR > Summernote
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Summernote extends Aksara
{
	public function __construct()
	{
		parent::__construct();
		
		if(!get_userdata('is_logged'))
		{
			redirect(base_url());
		}
		
		$this->permission->must_ajax(base_url());
	}
	
	public function upload()
	{
		if(!is_dir(UPLOAD_PATH . '/summernote'))
		{
			if(mkdir(UPLOAD_PATH . '/summernote', 0755, true))
			{
				copy(UPLOAD_PATH . '/placeholder.png', UPLOAD_PATH . '/summernote/placeholder.png');
			}
		}
		
		$config['allowed_types'] 					= IMAGE_FORMAT_ALLOWED;
		$config['upload_path'] 						= UPLOAD_PATH . '/summernote';
		$config['max_size']      					= (is_numeric(MAX_UPLOAD_SIZE) ? MAX_UPLOAD_SIZE : 1024*2);
		$config['encrypt_name']	 					= TRUE;
		
		/* load and initialize the library */
		$this->load->library('upload');
		$this->upload->initialize($config);
		
		if($this->upload->do_upload('image'))
		{
			$upload_data							= $this->upload->data();
			
			/* compress image */
			$config['image_library']				= 'gd2';
			$config['source_image']					= UPLOAD_PATH . '/summernote/' . $upload_data['file_name'];
			$config['create_thumb']					= false;
			$config['maintain_ratio']				= true;
			$config['width']						= 800;
			$config['height']						= 800;
			$config['new_image']					= UPLOAD_PATH . '/summernote/' . $upload_data['file_name'];
			$this->load->library('image_lib', $config);
			$this->image_lib->resize();
			
			return make_json
			(
				array
				(
					'status'						=> 'success',
					'source'						=> get_image('summernote', $upload_data['file_name'])
				)
			);
		}
		
		return make_json
		(
			array
			(
				'status'							=> 'error',
				'messages'							=> phrase('upload_error')
			)
		);
	}
	
	public function delete()
	{
		$filename									= basename($this->input->post('source'));
		if(file_exists(UPLOAD_PATH . '/summernote/' . $filename))
		{
			@unlink(UPLOAD_PATH . '/summernote/' . $filename);
			
			return make_json
			(
				array
				(
					'status'						=> 'success',
					'messages'						=> phrase('image_was_successfully_removed')
				)
			);
		}
		
		return make_json
		(
			array
			(
				'status'							=> 'error',
				'messages'							=> phrase('image_not_found')
			)
		);
	}
}