<?php

namespace Aksara\Libraries;

/**
 * Miscellaneous Library
 * This class is used to generate any miscellanious features
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.2.4
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Miscellaneous
{
	public function __construct()
	{
	}
	
	/**
	 * qrcode generator
	 */
	public function qrcode_generator($params = null)
	{
		$generator									= new \chillerlan\QRCode\QRCode();
		
		if(!file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . '_qrcode' . DIRECTORY_SEPARATOR . sha1(json_encode($params)) . '.png'))
		{
			if(!is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . '_qrcode'))
			{
				mkdir(UPLOAD_PATH . DIRECTORY_SEPARATOR . '_qrcode', 0755, true);
			}
			
			$data									= $generator->render($params);

			list($type, $data)						= explode(';', $data);
			list(, $data)							= explode(',', $data);
			$data									= base64_decode($data);
			
			file_put_contents(UPLOAD_PATH . DIRECTORY_SEPARATOR . '_qrcode' . DIRECTORY_SEPARATOR . sha1(json_encode($params)) . '.png', $data);
		}
		
		return get_image('_qrcode', sha1(json_encode($params)) . '.png');
	}
	
	/**
	 * barcode generator
	 */
	public function barcode_generator($params = null)
	{
		$generator									= new \Picqer\Barcode\BarcodeGeneratorPNG();
		
		if(!file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . '_barcode' . DIRECTORY_SEPARATOR . sha1(json_encode($params)) . '.png'))
		{
			if(!is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . '_barcode'))
			{
				mkdir(UPLOAD_PATH . DIRECTORY_SEPARATOR . '_barcode', 0755, true);
			}
			
			$data									= $generator->getBarcode($params, $generator::TYPE_CODE_128, 1, 60);
			
			file_put_contents(UPLOAD_PATH . DIRECTORY_SEPARATOR . '_barcode' . DIRECTORY_SEPARATOR . sha1(json_encode($params)) . '.png', $data);
		}
		
		return get_image('_barcode', sha1(json_encode($params)) . '.png');
	}
	
	/**
	 * shortlink generator
	 */
	public function shortlink_generator($params = null, $slug = null, $data = array())
	{
		if(!$params) return false;
		
		$this->model								= new \Aksara\Laboratory\Model();
		
		// hash generator
		$hash										= substr(sha1(uniqid('', true)), -6);
		
		// check if hash already present
		if($this->model->get_where('app__shortlink', array('hash' => $hash), 1)->row())
		{
			// hash already present, repeat generator
			$this->shortlink_generator($params);
		}
		
		$checker									= $this->model->get_where('app__shortlink', array('url' => $params), 1)->row();
		
		// check if parameter already present
		if($checker)
		{
			$hash									= $checker->hash;
		}
		else
		{
			// no data present, insert one
			$this->model->insert
			(
				'app__shortlink',
				array
				(
					'hash'							=> $hash,
					'url'							=> $params,
					'data'							=> json_encode($data)
				)
			);
		}
		
		return base_url(($slug ? $slug : 'shortlink') . '/' . $hash);
	}
}
