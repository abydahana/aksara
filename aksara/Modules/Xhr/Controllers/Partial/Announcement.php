<?php

namespace Aksara\Modules\Xhr\Controllers\Partial;

/**
 * XHR > Partial > Announcement
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.2.8
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Announcement extends \Aksara\Laboratory\Core
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$query										= $this->model->order_by('announcement_id', 'desc')->get_where
		(
			'app__announcements',
			array
			(
				'status'							=> 1,
				'placement'							=> (service('request')->getGet('placement') ? 'backend' : 'frontend'),
				'start_date <= '					=> date('Y-m-d'),
				'end_date >= '						=> date('Y-m-d')
			),
			10
		)
		->result();
		
		$output										= array();
		
		if($query)
		{
			foreach($query as $key => $val)
			{
				$output[]							= array
				(
					'label'							=> $val->title,
					'url'							=> base_url('announcements/' . $val->announcement_slug)
				);
			}
		}
		
		return make_json($output);
	}
}
