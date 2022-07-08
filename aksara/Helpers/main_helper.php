<?php
/**
 * Main Helper
 * A helper that required by Aksara
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

if(!function_exists('generate_token'))
{
	/**
	 * Generate security token to validate the query string values
	 */
	function generate_token($data = array())
	{
		if(isset($data['aksara']))
		{
			unset($data['aksara']);
		}
		
		if(is_array($data))
		{
			$data									= http_build_query($data);
		}
		
		return substr(sha1($data . ENCRYPTION_KEY . get_userdata('session_generated')), 6, 6);
	}
}

if(!function_exists('aksara_header'))
{
	/**
	 * include additional css
	 */
	function aksara_header()
	{
		$stylesheet									= null;
		$backtrace									= debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
		
		if(isset($backtrace[0]['file']) && file_exists(str_replace('layout.php', 'package.json', $backtrace[0]['file'])))
		{
			$package								= file_get_contents(str_replace('layout.php', 'package.json', $backtrace[0]['file']));
			$package								= json_decode(($package ? $package : '[]'));
			
			if(isset($package->colorscheme))
			{
				$stylesheet							= '
					body
					{
						' . (isset($package->colorscheme->page->background) && valid_hex($package->colorscheme->page->background) ? 'background: ' . $package->colorscheme->page->background . '!important;' : null) . '
						' . (isset($package->colorscheme->page->text) && valid_hex($package->colorscheme->page->text) ? 'color: ' . $package->colorscheme->page->text . '!important;' : null) . '
					}
					.aksara-header:not(.bg-transparent)
					{
						' . (isset($package->colorscheme->header->background) && valid_hex($package->colorscheme->header->background) ? 'background: ' . $package->colorscheme->header->background . '!important;' : null) . '
						' . (isset($package->colorscheme->header->text) && valid_hex($package->colorscheme->header->text) ? 'color: ' . $package->colorscheme->header->text . '!important;' : null) . '
					}
					.aksara-header > * > ul > li > a,
					.aksara-header.navbar-dark > * > ul > li > a,
					.aksara-header.navbar-light > * > ul > li > a
					{
						' . (isset($package->colorscheme->header->text) && valid_hex($package->colorscheme->header->text) ? 'color: ' . $package->colorscheme->header->text . '!important;' : null) . '
					}
					.aksara-sidebar
					{
						' . (isset($package->colorscheme->sidebar->background) && valid_hex($package->colorscheme->sidebar->background) ? 'background: ' . $package->colorscheme->sidebar->background . '!important;' : null) . '
						' . (isset($package->colorscheme->sidebar->text) && valid_hex($package->colorscheme->sidebar->text) ? 'color: ' . $package->colorscheme->sidebar->text . '!important;' : null) . '
					}
					.aksara-sidebar a
					{
						' . (isset($package->colorscheme->sidebar->text) && valid_hex($package->colorscheme->sidebar->text) ? 'color: ' . $package->colorscheme->sidebar->text . '!important;' : null) . '
					}
					.aksara-footer
					{
						' . (isset($package->colorscheme->footer->background) && valid_hex($package->colorscheme->footer->background) ? 'background: ' . $package->colorscheme->footer->background . '!important;' : null) . '
						' . (isset($package->colorscheme->footer->text) && valid_hex($package->colorscheme->footer->text) ? 'color: ' . $package->colorscheme->footer->text . '!important;' : null) . '
					}
					.aksara-footer a
					{
						' . (isset($package->colorscheme->footer->text) && valid_hex($package->colorscheme->footer->text) ? 'color: ' . $package->colorscheme->footer->text . '!important;' : null) . '
					}
				';
			}
		}
		
		$output										= '<meta name="_token" content="' . sha1(current_page() . ENCRYPTION_KEY . get_userdata('session_generated')) . '" />' . "\n";
		$output										.= '<link rel="stylesheet" type="text/css" href="' . base_url('assets/css/styles.min.css') . '" />' . "\n";
		$output										.= '<link rel="stylesheet" type="text/css" href="' . base_url('assets/materialdesignicons/css/materialdesignicons.min.css') . '" />' . "\n";
		$output										.= '<script type="text/javascript">(function(w,d,u){w.readyQ=[];w.bindReadyQ=[];function p(x,y){if(x=="ready"){w.bindReadyQ.push(y)}else{w.readyQ.push(x)}};var a={ready:p,bind:p};w.$=w.jQuery=function(f){if(f===d||f===u){return a}else{p(f)}}})(window,document)</script>' . "\n";
		
		if($stylesheet)
		{
			$output									.= '<style type="text/css">' . $stylesheet . '</style>';
		}
		
		return $output;
	}
}

if(!function_exists('aksara_footer'))
{
	/**
	 * include additional js
	 */
	function aksara_footer()
	{
		$output										= show_flashdata() . "\n";
		
		if(get_setting('facebook_app_id') && !get_setting('disqus_site_domain'))
		{
			$output									.= '<script>window.fbAsyncInit = function() {FB.init({appId: \'' . get_setting('facebook_app_id') . '\', autoLogAppEvents: true, xfbml: true, version: \'v9.0\'});};</script>' . "\n";
			$output									.= '<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>' . "\n";
		}
		
		if(get_setting('google_analytics_key'))
		{
			$output									.= '<script async src="https://www.googletagmanager.com/gtag/js?id=' . get_setting('google_analytics_key') . '"></script>' . "\n";
			$output									.= '<script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag(\'js\', new Date());gtag(\'config\', \'' . get_setting('google_analytics_key') . '\');</script>' . "\n";
		}
		
		$output										.= '<script type="text/javascript" src="' . base_url('assets/js/scripts.min.js') . '"></script>' . "\n";
		$output										.= '<script type="text/javascript">(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>' . "\n";
		
		return $output;
	}
}

if(!function_exists('load_comment_plugin'))
{
	/**
	 * A message exception that will throw as JSON
	 */
	function load_comment_plugin($url = null)
	{
		// disqus comment plugin
		if(get_setting('disqus_site_domain'))
		{
			return '
				<div id="disqus_thread" class="mt-5 mb-5"></div>
				<div id="disqus_script"></div>
				<script>
					if(typeof DISQUS === "undefined")
					{
						(function()
						{
							var t					= document,
								e					= t.createElement("script");
							e.src					= "https://' . str_replace(array('http://','https://'), '', get_setting('disqus_site_domain')) . '/embed.js",
							e.setAttribute("data-timestamp", +new Date),
							t.getElementById("disqus_script").appendChild(e)
						})();
					}
					else
					{
						DISQUS.reset
						({
							reload: true,
							config: function()
							{
								this.page.url = "' . ($url ? $url : current_page()) . '";
							}
						})
					}
				</script>
			';
		}
		
		// facebook comment plugin
		else if(get_setting('facebook_app_id'))
		{
			return '
				<div class="fb-comments-container mt-5 mb-5">
					<div class="fb-comments" data-href="' . $url . '" data-numposts="5" data-width="100%"></div>
				</div>
			';
		}
		
		return false;
	}
}

if(!function_exists('throw_exception'))
{
	/**
	 * A message exception that will throw as JSON
	 */
	function throw_exception($code = 500, $data = array(), $target = null, $redirect = false)
	{
		/* check if the request isn't through xhr */
		if(!service('request')->isAJAX())
		{
			if(!$target)
			{
				$target								= base_url();
			}
			
			/* check if data isn't an array */
			if($data && !is_array($data))
			{
				/* set the flashdata */
				if(in_array($code, array(200, 301)))
				{
					/* success */
					service('session')->setFlashdata('success', $data);
				}
				else if(in_array($code, array(403, 404)))
				{
					/* warning */
					service('session')->setFlashdata('warning', $data);
				}
				else
				{
					/* unexpected error */
					service('session')->setFlashdata('error', $data);
				}
			}
			
			/* redirect into target */
			redirect_to($target);
		}
		
		$exception									= array();
		
		if(is_array($data))
		{
			foreach($data as $key => $val)
			{
				$key								= str_replace('[]', '', $key);
				$exception[$key]					= $val;
			}
		}
		else
		{
			$exception								= $data;
		}
		
		$output										= json_encode
		(
			array
			(
				'status'							=> $code,
				'message'							=> $exception,
				'target'							=> ($target ? $target : ''),
				'redirect'							=> $redirect
			)
		);
		
		http_response_code($code);
		
		header('Content-Type: application/json');
		
		exit($output);
	}
}

if(!function_exists('show_flashdata'))
{
	/**
	 * Generate flashdata
	 */
	function show_flashdata()
	{
		if(service('session')->getFlashdata())
		{
			return '
				<div class="toast-container position-fixed bottom-0 end-0 p-3">
					<div class="toast align-items-center text-bg-' . (service('session')->getFlashdata('success') ? 'success' : (service('session')->getFlashdata('warning') ? 'warning' : 'danger')) . ' fade show" role="alert" aria-live="assertive" aria-atomic="true">
						<div class="d-flex">
							<div class="toast-body">
								<div class="row align-items-center">
									<div class="col-2">
										<i class="mdi mdi-' .(service('session')->getFlashdata('success') ? 'check-circle-outline' : (service('session')->getFlashdata('warning') ? 'alert-octagram-outline' : 'emoticon-sad-outline')) . ' mdi-2x"></i>
									</div>
									<div class="col-10 text-break">
										' . (service('session')->getFlashdata('success') ? service('session')->getFlashdata('success') : (service('session')->getFlashdata('warning') ? service('session')->getFlashdata('warning') : service('session')->getFlashdata('error'))) . '
									</div>
								</div>
							</div>
							<button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="' . phrase('close') . '"></button>
						</div>
					</div>
				</div>
			';
		}
		
		return false;
	}
}

if(!function_exists('format_slug'))
{
	/**
	 * Generate slug from given string
	 */
	function format_slug($string = null)
	{
		$string										= strtolower(preg_replace('/[\-\s]+/', '-', preg_replace('/[^A-Za-z0-9-]+/', '-', trim($string))));
		
		if(!preg_match('/(\d{10})/', $string))
		{
			$string									= $string;
		}
		
		return $string;
	}
}

if(!function_exists('valid_hex'))
{
	/**
	 * Validate hex color
	 */
	function valid_hex($string = null)
	{
		if($string && preg_match('/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $string))
		{
			return true;
		}
		
		return false;
	}
}

if(!function_exists('array_sort'))
{
	/**
	 * Sort array
	 */
	function make_cmp($data = array())
	{
		return function ($a, $b) use (&$data)
		{
			foreach ($data as $column => $sort)
			{
				if(!$sort)
				{
					$sort							= 'asc';
				}
				$diff								= strcmp((is_object($a) ? $a->$column : $a[$column]), (is_object($b) ? $b->$column : $b[$column]));
				if($diff !== 0)
				{
					if('asc' === strtolower($sort))
					{
						return $diff;
					}
					return $diff * -1;
				}
			}
			return 0;
		};
	}
	
	function array_sort($data = null, $order_by = array(), $sort = 'asc')
	{
		if(!is_array($order_by) && is_string($order_by))
		{
			$order_by								= array($order_by => $sort);
		}
		usort($data, make_cmp($order_by));
		return $data;
	}
}

if(!function_exists('reset_sort'))
{
	/**
	 * Reset Sort
	 */
	function reset_sort($resource = array())
	{
		$is_numeric									= false;
		
		foreach($resource as $key => $val)
		{
			if(is_array($val))
			{
				$resource[$key]						= reset_sort($val);
			}
			
			if(is_numeric($key))
			{
				$is_numeric							= true;
			}
		}
		
		if($is_numeric)
		{
			return array_values($resource);
		}
		else
		{
			return $resource;
		}
		
		return array_values($resource);
	}
}

if(!function_exists('number2alpha'))
{
	/*
	 * Convert an integer to a string of uppercase letters (A-Z, AA-ZZ, AAA-ZZZ, etc.)
	 */
	function number2alpha($number = 0, $suffix = null)
	{
		for($alpha = ''; $number >= 0; $number = intval($number / 26) - 1)
		{
			$alpha									= chr($number % 26 + 0x41) . $alpha;
		}
		
		return $alpha . $suffix;
	}
}

if(!function_exists('alpha2number'))
{
	/*
	 * Convert a string of uppercase letters to an integer.
	 */
	function alpha2number($alpa = null, $suffix = null)
	{
		$length										= strlen($alpha);
		$number										= 0;
		
		for($i = 0; $i < $l; $i++)
		{
			$number									= $number * 26 + ord($alpha[$i]) - 0x40;
		}
		
		return ($number - 1) . $suffix;
	}
}

if(!function_exists('encrypt'))
{
	/*
	 * Encryption
	 */
	function encrypt($passphrase = null)
	{
		if(!$passphrase) return false;
		
		$encrypter									= \Config\Services::encrypter();
		
		return base64_encode($encrypter->encrypt($passphrase));
	}
}

if(!function_exists('decrypt'))
{
	/*
	 * Encryption
	 */
	function decrypt($source = null)
	{
		if(!$source) return false;
		
		$encrypter									= \Config\Services::encrypter();
		
		return $encrypter->decrypt(base64_decode($source));
	}
}
