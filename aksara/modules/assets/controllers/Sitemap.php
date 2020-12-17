<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Assets > Sitemap
 * Generate the sitemap, will be route into sitemap.xml
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Sitemap extends Aksara
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$pages										= $this->model
		->select
		('
			page_slug AS loc,
			DATE_FORMAT(updated_timestamp, "%Y-%m-%d") AS lastmod,
			"monthly" AS changefreq,
			"0.8" AS priority
		')
		->get_where
		(
			'pages',
			array
			(
				'status'							=> 1
			)
		)
		->result();
		
		$static_pages								= array
		(
			'features/point-of-sale',
			'features/order-management',
			'features/product-management',
			'features/inventory-management',
			'features/expense-management',
			'features/complete-reporting',
			'features/multi-outlet',
			'features/employee-management',
			'features/customer-management',
			'features/table-management',
			'features/tax-management',
			'features/various-pricing',
			'features/discount-management',
			'features/free-directory',
			'features/multi-platform',
			'features/cloud-everything',
			
			'business/cafe',
			'business/culinary',
			'business/fashion',
			'business/groceries',
			'business/bakery',
			'business/fruit-shop',
			'business/retail',
			'business/barbershop',
			'business/beauty',
			'business/jewelry'
		);
		
		foreach($static_pages as $key => $val)
		{
			$pages[]								= (object) array
			(
				'loc'								=> $val,
				'lastmod'							=> date('Y-m-d', strtotime('-1 month')),
				'changefreq'						=> 'monthly',
				'priority'							=> '0.8'
			);
		}
		
		$blogs										= $this->model
		->select
		('
			category_slug,
			post_slug AS loc,
			DATE_FORMAT(updated_timestamp, "%Y-%m-%d") AS lastmod,
			"monthly" AS changefreq,
			"0.8" AS priority
		')
		->join
		(
			'blogs__categories',
			'blogs__categories.category_id = blogs.post_category'
		)
		->get_where
		(
			'blogs',
			array
			(
				'blogs.status'						=> 1
			)
		)
		->result();
		
		$blogs_categories							= $this->model
		->select
		('
			category_slug AS loc,
			"' . date('Y-m-d') . '" AS lastmod,
			"monthly" AS changefreq,
			"0.8" AS priority
		')
		->get_where
		(
			'blogs__categories',
			array
			(
				'status'							=> 1
			)
		)
		->result();
		
		$galleries									= $this->model
		->select
		('
			gallery_slug AS loc,
			DATE_FORMAT(updated_timestamp, "%Y-%m-%d") AS lastmod,
			"monthly" AS changefreq,
			"0.8" AS priority
		')
		->get_where
		(
			'galleries',
			array
			(
				'status'							=> 1
			)
		)
		->result();
		
		$announcements								= $this->model
		->select
		('
			announcement_slug AS loc,
			DATE_FORMAT(updated_timestamp, "%Y-%m-%d") AS lastmod,
			"monthly" AS changefreq,
			"0.8" AS priority
		')
		->get_where
		(
			'app__announcements',
			array
			(
				'status'							=> 1
			)
		)
		->result();
		
		$xml										= new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');
		
		if($pages)
		{
			foreach($pages as $key => $val)
			{
				$val->loc							= base_url('pages/' . $val->loc);
				$node								= $xml->addChild('url');
				$this->array_to_xml($val, $node);
			}
		}
		
		if($blogs)
		{
			foreach($blogs as $key => $val)
			{
				$val->loc							= base_url('blogs/' . $val->category_slug . '/' . $val->loc);
				unset($val->category_slug);
				$node								= $xml->addChild('url');
				$this->array_to_xml($val, $node);
			}
		}
		
		if($blogs_categories)
		{
			foreach($blogs_categories as $key => $val)
			{
				$val->loc							= base_url('blogs/' . $val->loc);
				$node								= $xml->addChild('url');
				$this->array_to_xml($val, $node);
			}
		}
		
		if($galleries)
		{
			foreach($galleries as $key => $val)
			{
				$val->loc							= base_url('galleries/' . $val->loc);
				$node								= $xml->addChild('url');
				$this->array_to_xml($val, $node);
			}
		}
		
		if($announcements)
		{
			foreach($announcements as $key => $val)
			{
				$val->loc							= base_url('announcements/' . $val->loc);
				$node								= $xml->addChild('url');
				$this->array_to_xml($val, $node);
			}
		}
		
		$this->output->set_content_type('xml');
		$this->output->set_output($xml->asXML());
	}
	
	private function array_to_xml($array = array(), &$node)
	{
		foreach($array as $key => $val)
		{
			if(is_array($val))
			{
				if(!is_numeric($key))
				{
					$subnode						= $node->addChild($key);
					array_to_xml($val, $subnode);
				}
				else
				{
					array_to_xml($val, $node);
				}
			}
			else
			{
				$node->addChild($key, $val);
			}
		}
	}
}
