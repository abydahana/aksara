<?php defined('BASEPATH') OR exit('No direct script access allowed');
function compress()
{
	$CI												=& get_instance();
	$buffer											= $CI->output->get_output();
	$content_type									= $CI->output->get_content_type();
	
	/* make a backup of "pre" tag */
	preg_match_all('#\<pre.*\>(.*)\<\/pre\>#Uis', $buffer, $pre_backup);
	$buffer											= str_replace($pre_backup[0], array_map(function($element){return '<pre>' . $element . '</pre>';}, array_keys($pre_backup[0])), $buffer);

	$buffer											= preg_replace(array('/[\n\t\s]+/', '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/(\>)\s*(\<)/m'), array(' ', '>', '<', '\\1', '$1$2'), $buffer);
	
	/* rollback the pre tag */
	$buffer											= str_replace(array_map(function($element){return '<pre>' . $element . '</pre>';}, array_keys($pre_backup[0])), $pre_backup[0], $buffer);
	
	if(!in_array($content_type, array('application/json', 'application/xml', 'text/xml', 'image/svg+xml')))
	{
		$author										= '
 * سْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ
 * 
 * This application was built with Aksara CMS, a powerful engine to realize
 * what\'s only  in your  dreams  becomes  something real.  Search or  visit
 * our website to prove it.
 * 
 * @author			Aby Dahana
 * @website			www.aksaracms.com
 ';
	
		if(in_array($content_type, array('application/x-javascript', 'text/javascript', 'text/css')))
		{
			$author									= "/**" . $author . "*/\n";
		}
		else
		{
			$author									= '<!--' . $author . '* -->';
		}
	}
	else
	{
		$author										= null;
	}
	
	$CI->output->set_output($author . $buffer);
	$CI->output->_display();
}

/* End of file compress.php */
/* Location: ./system/application/hooks/compress.php */