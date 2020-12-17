<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Assets > Manifest
 * Add the manifest property into the application, will be route to manifest.json.
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Manifest extends Aksara
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		make_json
		(
			array
			(
				'name'								=> get_setting('app_name'),
				'short_name'						=> get_setting('app_name'),
				'start_url'							=> '/',
				'display'							=> 'standalone',
				'icons'								=> array
				(
					array
					(
						'src'						=> get_image('settings', '128x128__' . get_setting('app_icon'), 'icon'),
						'sizes'						=> '128x128',
						'type'						=> get_mime_by_extension(get_image('settings', '128x128__' . get_setting('app_icon'), 'icon'))
					),
					array
					(
						'src'						=> get_image('settings', '144x144__' . get_setting('app_icon'), 'icon'),
						'sizes'						=> '144x144',
						'type'						=> get_mime_by_extension(get_image('settings', '144x144__' . get_setting('app_icon'), 'icon'))
					),
					array
					(
						'src'						=> get_image('settings', '152x152__' . get_setting('app_icon'), 'icon'),
						'sizes'						=> '152x152',
						'type'						=> get_mime_by_extension(get_image('settings', '152x152__' . get_setting('app_icon'), 'icon'))
					),
					array
					(
						'src'						=> get_image('settings', '192x192__' . get_setting('app_icon'), 'icon'),
						'sizes'						=> '192x192',
						'type'						=> get_mime_by_extension(get_image('settings', '192x192__' . get_setting('app_icon'), 'icon'))
					),
					array
					(
						'src'						=> get_image('settings', '512x512__' . get_setting('app_icon'), 'icon'),
						'sizes'						=> '512x512',
						'type'						=> get_mime_by_extension(get_image('settings', '512x512__' . get_setting('app_icon'), 'icon'))
					)
				),
				'background_color'					=> (get_customization('header_background') ? get_customization('header_background') : '#fafafa'),
				'theme_color'						=> (get_customization('page_background') ? get_customization('page_background') : '#ffffff')
			)
		);
	}
}
